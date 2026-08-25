# LaunchHR MCP server

Exposes LaunchHR's AI tools (time off, absence reporting) to any [MCP](https://modelcontextprotocol.io)-compatible client — Claude Desktop, Claude Code, and others.

This server doesn't hardcode a tool list. On startup it calls LaunchHR's `GET /api/{company}/ai-tools` endpoint to fetch the current tool definitions, and every call is proxied to `POST /api/{company}/ai-tools/{tool}/invoke`. Both endpoints run through the exact same permission-checked domain services as the rest of the app (`CreateTimeOff`, `DestroyTimeOff`, etc.) — this server has no direct database access and can't do anything the token's owner couldn't already do through the LaunchHR UI.

## Setup

1. In LaunchHR, go to your profile → **API tokens** and create a token.
2. Copy `.env.example` to `.env` and fill in `LAUNCHHR_BASE_URL`, `LAUNCHHR_API_TOKEN`, and `LAUNCHHR_COMPANY_ID`.
3. Install and build:
   ```bash
   npm install
   npm run build
   ```

## Connecting to Claude Desktop or Claude Code

Add to your MCP client's config (e.g. `claude_desktop_config.json`):

```json
{
  "mcpServers": {
    "launchhr": {
      "command": "node",
      "args": ["/absolute/path/to/mcp-server/dist/index.js"],
      "env": {
        "LAUNCHHR_BASE_URL": "https://your-launchhr-domain.example.com",
        "LAUNCHHR_API_TOKEN": "your-token-here",
        "LAUNCHHR_COMPANY_ID": "4"
      }
    }
  }
}
```

Restart the client, and the LaunchHR tools (`list_my_time_off`, `request_time_off`, `cancel_time_off`, `get_absence_report`, `search_employees`) will be available.

## Deploying it as its own service

This is a standalone Node process — nothing here is Laravel-specific — so it can run as its own Railway service (or anywhere else that runs a long-lived Node process) alongside the main HRPlatform app, using the same environment variables from `.env.example`.
