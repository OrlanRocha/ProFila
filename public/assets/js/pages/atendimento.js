import { apiPost } from "../api.js";
import { confirmWithReason, toastError, toastOk } from "../ui.js";

let current = { ticket_id: null, display: null, status: null };

const els = {
  queueId: document.getElementById("queueId"),
  pointId: document.getElementById("pointId"),
  currentDisplay: document.getElementById("currentDisplay"),
  currentStatus: document.getElementById("currentStatus"),
  feed: document.getElementById("feed"),
  btnNext: document.getElementById("btnNext"),
  btnStart: document.getElementById("btnStart"),
  btnFinish: document.getElementById("btnFinish"),
  btnRecall: document.getElementById("btnRecall"),
  btnCancel: document.getElementById("btnCancel"),
  btnReinsert: document.getElementById("btnReinsert"),
};

function pushFeed(text) {
  const li = document.createElement("li");
  li.className = "px-3 py-2 rounded-xl bg-slate-900/50 border border-slate-800";
  li.textContent = text;
  els.feed.prepend(li);
}

function setCurrent({ ticket_id, display, status }) {
  current.ticket_id = ticket_id;
  current.display = display;
  current.status = status;

  els.currentDisplay.textContent = display || "—";
  els.currentStatus.textContent = status || "—";

  const has = !!ticket_id;
  els.btnStart.disabled = !(has && status === "CALLED");
  els.btnFinish.disabled = !(has && status === "IN_SERVICE");
  els.btnRecall.disabled = !(has && (status === "CALLED" || status === "IN_SERVICE"));
  els.btnCancel.disabled = !(has && (status === "WAITING" || status === "CALLED"));
  els.btnReinsert.disabled = !(has && (status === "CALLED" || status === "WAITING"));
}

els.btnNext?.addEventListener("click", async () => {
  const queue_id = Number(els.queueId.value);
  const point_id = Number(els.pointId.value);

  const r = await apiPost("/api/tickets/next", { queue_id, point_id });
  if (!r.ok) return toastError(r.msg || "Sem senha / concorrência");

  const { ticket_id, display } = r.data;
  setCurrent({ ticket_id, display, status: "CALLED" });
  pushFeed(`Chamou ${display} (ticket ${ticket_id})`);
  toastOk("Senha chamada.");
});

els.btnStart?.addEventListener("click", async () => {
  const point_id = Number(els.pointId.value);
  const r = await apiPost("/api/tickets/start", { ticket_id: current.ticket_id, point_id });
  if (!r.ok) return toastError(r.msg || "Falha ao iniciar");

  setCurrent({ ...current, status: "IN_SERVICE" });
  pushFeed(`Iniciou atendimento de ${current.display}`);
  toastOk("Atendimento iniciado.");
});

els.btnFinish?.addEventListener("click", async () => {
  const point_id = Number(els.pointId.value);
  const r = await apiPost("/api/tickets/finish", { ticket_id: current.ticket_id, point_id });
  if (!r.ok) return toastError(r.msg || "Falha ao finalizar");

  pushFeed(`Finalizou ${current.display}`);
  toastOk("Finalizado.");
  setCurrent({ ticket_id: null, display: null, status: null });
});

els.btnCancel?.addEventListener("click", async () => {
  const c = await confirmWithReason({ title: "Cancelar senha?", confirmText: "Cancelar", placeholder: "Motivo (obrigatório)" });
  if (!c.confirmed) return;

  const r = await apiPost("/api/tickets/cancel", { ticket_id: current.ticket_id, reason: c.reason });
  if (!r.ok) return toastError(r.msg || "Falha ao cancelar");

  pushFeed(`Cancelou ${current.display} — ${c.reason}`);
  toastOk("Cancelada.");
  setCurrent({ ticket_id: null, display: null, status: null });
});

els.btnReinsert?.addEventListener("click", async () => {
  const c = await confirmWithReason({ title: "Reinserir na fila?", confirmText: "Reinserir", placeholder: "Motivo (obrigatório)" });
  if (!c.confirmed) return;

  const r = await apiPost("/api/tickets/reinsert", { ticket_id: current.ticket_id, reason: c.reason });
  if (!r.ok) return toastError(r.msg || "Falha ao reinserir");

  pushFeed(`Reinseriu ${current.display} — ${c.reason}`);
  toastOk("Reinserida na fila.");
  setCurrent({ ticket_id: null, display: null, status: null });
});

// Recall placeholder (endpoint opcional)
