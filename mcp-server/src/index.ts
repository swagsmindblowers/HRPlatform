import { McpServer } from '@modelcontextprotocol/server';
import { StdioServerTransport } from '@modelcontextprotocol/server/stdio';
import * as z from 'zod/v4';

const BASE_URL = requireEnv('LAUNCHHR_BASE_URL');
const API_TOKEN = requireEnv('LAUNCHHR_API_TOKEN');
const COMPANY_ID = requireEnv('LAUNCHHR_COMPANY_ID');

function requireEnv(name: string): string {
  const value = process.env[name];
  if (!value) {
    console.error(`Missing required environment variable: ${name}`);
    process.exit(1);
  }
  return value;
}

interface JsonSchemaProperty {
  type?: string;
  enum?: string[];
  description?: string;
}

interface ToolDefinition {
  name: string;
  description: string;
  input_schema: {
    properties?: Record<string, JsonSchemaProperty>;
    required?: string[];
  };
}

/**
 * LaunchHR's tool schemas only ever use simple leaf types (string, integer,
 * boolean, enum) with a flat `properties`/`required` shape - this covers
 * exactly that, rather than pulling in a general JSON-Schema-to-Zod library
 * for a handful of well-known shapes.
 */
function propertyToZod(property: JsonSchemaProperty): z.ZodTypeAny {
  let schema: z.ZodTypeAny;

  if (property.enum) {
    schema = z.enum(property.enum as [string, ...string[]]);
  } else {
    switch (property.type) {
      case 'string':
        schema = z.string();
        break;
      case 'integer':
        schema = z.number().int();
        break;
      case 'number':
        schema = z.number();
        break;
      case 'boolean':
        schema = z.boolean();
        break;
      default:
        schema = z.unknown();
    }
  }

  if (property.description) {
    schema = schema.describe(property.description);
  }

  return schema;
}

function toZodShape(inputSchema: ToolDefinition['input_schema']): Record<string, z.ZodTypeAny> {
  const properties = inputSchema.properties ?? {};
  const required = inputSchema.required ?? [];
  const shape: Record<string, z.ZodTypeAny> = {};

  for (const [key, property] of Object.entries(properties)) {
    const zodType = propertyToZod(property);
    shape[key] = required.includes(key) ? zodType : zodType.optional();
  }

  return shape;
}

async function launchHrFetch(path: string, init: RequestInit = {}): Promise<Response> {
  return fetch(`${BASE_URL}/api/${COMPANY_ID}${path}`, {
    ...init,
    headers: {
      ...init.headers,
      Authorization: `Bearer ${API_TOKEN}`,
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
  });
}

async function fetchToolDefinitions(): Promise<ToolDefinition[]> {
  const response = await launchHrFetch('/ai-tools');

  if (!response.ok) {
    throw new Error(`Failed to fetch tool definitions from LaunchHR (status ${response.status}).`);
  }

  const body = (await response.json()) as { data: ToolDefinition[] };
  return body.data;
}

async function invokeTool(name: string, input: unknown): Promise<unknown> {
  const response = await launchHrFetch(`/ai-tools/${name}/invoke`, {
    method: 'POST',
    body: JSON.stringify({ input }),
  });

  const body = (await response.json()) as { data?: unknown; error?: string };

  if (!response.ok) {
    throw new Error(body.error ?? `LaunchHR tool "${name}" failed (status ${response.status}).`);
  }

  return body.data;
}

async function main() {
  const server = new McpServer({ name: 'launchhr', version: '1.0.0' });
  const tools = await fetchToolDefinitions();

  for (const tool of tools) {
    server.registerTool(
      tool.name,
      {
        description: tool.description,
        inputSchema: z.object(toZodShape(tool.input_schema)),
      },
      async (args: unknown) => {
        try {
          const result = await invokeTool(tool.name, args);
          return { content: [{ type: 'text', text: JSON.stringify(result) }] };
        } catch (error) {
          return {
            content: [{ type: 'text', text: error instanceof Error ? error.message : String(error) }],
            isError: true,
          };
        }
      },
    );
  }

  const transport = new StdioServerTransport();
  await server.connect(transport);
}

main().catch((error) => {
  console.error('LaunchHR MCP server failed to start:', error);
  process.exit(1);
});
