import { apiPost } from "../api.js";
import { connectWS } from "../ws.js";

const ch = window.MONITOR?.channelId;
const linhas = window.MONITOR?.linhas || 1;

const wsStatus = document.getElementById("wsStatus");

function setLine(line, display, point) {
  const d = document.getElementById(`lineDisplay${line}`);
  const p = document.getElementById(`linePoint${line}`);
  if (d) d.textContent = display || "—";
  if (p) p.textContent = point ? `Mesa ${point}` : "—";
}

async function registerMonitor() {
  await apiPost("/api/monitors/register", { channel_id: ch }).catch(() => {});
}

await registerMonitor();

const wsUrl = window.PROFILA?.wsUrl || "ws://127.0.0.1:8080";
const room = `channel:${ch}`;

connectWS({
  url: wsUrl,
  rooms: [room],
  onMessage: (msg) => {
    wsStatus.textContent = `WS: online • ${room}`;

    if (msg.type === "TICKET_CALLED") {
      const pl = msg.payload || {};
      const line = Number(pl.line || 1);
      if (line >= 1 && line <= linhas) {
        setLine(line, pl.display, pl.point_id);
      }
    }
  },
  onOpen: () => {
    wsStatus.textContent = `WS: conectado • ${room}`;
  },
  onClose: () => {
    wsStatus.textContent = `WS: offline • ${room}`;
  }
});

wsStatus.textContent = `WS: conectando… • ${room}`;
