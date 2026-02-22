# Switch Widget (Zabbix 7.x)

Uses native Zabbix selection fields.

## Screenshot

![Switch widget](switch-widget.png)

## Configure

1. Select global `Host` (selection box style).
2. Set `Rows` and `Ports per row` (total ports = rows x ports per row).
3. Set optional `Brand` and `Model` text for switch bezel.
4. Set widget `Name` to control the dashboard widget title (header text).
5. Optional: set `Legend text` (leave empty to hide).
6. Optional: set traffic patterns (`Traffic in item pattern`, `Traffic out item pattern`) using `*` as port placeholder (example: `ifInOctets[*]`).
7. Optional: set `Port index start` for wildcard mapping (`*`), default `1` (set `0` for devices where first interface index is 0).
8. Optional: set `Speed item pattern` (example: `ifHighSpeed[*]`) for utilization calculations.
9. Optional: set `Show utilization overlay` to enable/disable heatmap strip + grid.
10. Optional: tune utilization thresholds (`Low`, `Warn`, `High`) and colors (`Low`, `Warn`, `High`, `n/a`).
11. Optional: select `Profile` to auto-fill layout (`Rows`, `Ports per row`, `SFP ports`, `Size (%)`, `Brand`, `Model`).
12. Optional: rename profile directly next to `Profile`.
13. Optional: use `Save current to selected profile` to overwrite selected profile (1-7).
14. Set optional `Size (%)` (40-100) to make switch compact.
15. Set optional `SFP ports` (0 = none, 2 = two extra SFP ports).
16. Select per-port `Trigger` from dropdown (for selected host).
17. Set per-port `Default color`, `Trigger OK color` and `Trigger NOK color`.
18. Optional: use `Bulk actions` to apply one color to all ports for each state.

Note: Trigger options are rendered server-side from selected host.
If you change host, reopen widget edit to refresh trigger lists.

## UI

- Widget renders with switch-style port front panel.
- Widget header title uses the widget `Name` field.
- Port status uses LED-like color indicator:
  - default color (light gray) = no trigger configured
  - trigger OK color = trigger configured and OK
  - trigger NOK color = active problem
- Port hover tooltip includes:
  - state/type/trigger text
  - IN/OUT sparkline + latest value (when user has item read permission)

## Release Notes 1.2.6

- Added integrated switch summary panel under the switch face.
- Added summary item-key configuration fields (software, VLANs, CPU, fan, uptime, serial).
- Added live per-port telemetry block in summary panel (IN/OUT sparklines, utilization, errors, discards, 24h state bars).
- Restored hover-driven port live updates with click/focus fallback.
- Removed redundant `Ports: ...` summary row.
- Improved sparkline visuals and sizing to better match switch style.
- Updated long item-pattern/key fields to 40 characters for consistency.
- Kept trigger LED behavior authoritative while utilization overlay remains optional.
- Fixed edit-form item suggestion popups opening all at once on load.
- Fixed click selection in item suggestion lists to work reliably with blur/focus handling.
- Added `Port index start` to support devices where interface indexing does not start at `1` (for example `0` on some platforms).
- Fixed maintenance badge logic to respect maintenance window timing (ended windows now show OFF correctly).
- Utilization heatmap layout now follows switch geometry (UTP block + separate SFP block), matching port placement better.

## Compatibility Note

- Primary target: Zabbix `7.0`.
- Should also work on Zabbix `7.2` and `7.4`.
- `7.2` and `7.4` are currently **not actively tested or maintained**.

## Profile Storage Permissions

Profiles are stored in `profiles.json` inside the module directory.  
The web/PHP user must be able to write this file when using `Save current to selected profile`.

Example (RHEL/Alma/Rocky with `apache`):

```bash
sudo chown root:apache /usr/share/zabbix/modules/switch/profiles.json
sudo chmod 664 /usr/share/zabbix/modules/switch/profiles.json
sudo chmod 755 /usr/share/zabbix/modules/switch
```

If SELinux is enabled, also set writable context:

```bash
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/usr/share/zabbix/modules/switch/profiles.json"
sudo restorecon -v /usr/share/zabbix/modules/switch/profiles.json
```

---

Parts of this software were generated using Codex. We do not guarantee the total accuracy, security, or stability of the generated code.
