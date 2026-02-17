# Switch Widget (Zabbix 7.0)

Uses native Zabbix selection fields.

## Configure

1. Select global `Host group` (selection box style).
2. Select global `Host` (selection box style).
3. Set `Rows` and `Ports per row` (total ports = rows x ports per row).
4. Set optional `Brand` and `Model` text for switch bezel.
5. Optional: set `Legend text` (leave empty to hide).
6. Optional: select `Profile` to auto-fill layout (`Rows`, `Ports per row`, `SFP ports`, `Size (%)`).
7. Edit `User profile 1..3` blocks to create your own reusable presets.
8. Set optional `Size (%)` (40-100) to make switch compact.
9. Set optional `SFP ports` (0 = none, 2 = two extra SFP ports).
10. Select per-port `Trigger` from dropdown (for selected host).
11. Set default/trigger colors per port (with color picker).
12. Optional: use `Bulk actions` to apply one default/trigger color to all ports.

Note: Trigger options are rendered server-side from selected host.
If you change host, reopen widget edit to refresh trigger lists.

## UI

- Widget renders with switch-style port front panel.
- Port status uses LED-like color indicator:
  - default color = normal
  - trigger color = active problem
