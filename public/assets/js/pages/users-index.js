import { apiGet, apiPost } from "../api.js";
import { confirmWithReason, toastError, toastOk } from "../ui.js";

function maskCPF(cpf) {
  if (!cpf) return "";
  return cpf.replace(/\d(?=\d{2})/g, "*");
}

function badge(active) {
  if (Number(active) === 1) {
    return `<span class="px-2 py-1 text-xs rounded-lg bg-emerald-500/20 text-emerald-200 border border-emerald-600/40">ATIVO</span>`;
  }
  return `<span class="px-2 py-1 text-xs rounded-lg bg-rose-500/20 text-rose-200 border border-rose-600/40">INATIVO</span>`;
}

$(async function () {
  const table = $("#usersTable").DataTable({
    ajax: async (_data, cb) => {
      const r = await apiGet("/api/users");
      if (!r.ok) { toastError(r.msg || "Falha ao carregar usuários"); return cb({ data: [] }); }
      cb({ data: r.data || [] });
    },
    columns: [
      { data: "ativo", render: (v) => badge(v) },
      { data: "nome" },
      { data: "email" },
      { data: "cpf", render: (v) => maskCPF(v) },
      { data: "role_nome", defaultContent: "" },
      {
        data: null, orderable: false, render: (row) => {
          const id = row.id;
          return `
            <div class="flex gap-2">
              <a class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold" href="/usuarios/${id}/editar">Editar</a>
              <a class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold" href="/usuarios/${id}/escopos">Escopos</a>
              <button class="btnToggle px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold" data-id="${id}" data-active="${row.ativo}">
                ${Number(row.ativo) === 1 ? "Inativar" : "Ativar"}
              </button>
              <button class="btnReset px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold" data-id="${id}">Reset senha</button>
            </div>
          `;
        }
      }
    ],
    pageLength: 25,
    order: [[1, "asc"]]
  });

  $("#usersTable").on("click", ".btnToggle", async function () {
    const id = Number(this.dataset.id);
    const active = Number(this.dataset.active);
    const next = active === 1 ? 0 : 1;

    const c = await confirmWithReason({
      title: next === 0 ? "Inativar usuário?" : "Ativar usuário?",
      confirmText: next === 0 ? "Inativar" : "Ativar",
      placeholder: "Motivo (obrigatório)"
    });
    if (!c.confirmed) return;

    const r = await apiPost("/api/users/toggle", { id, active: next, reason: c.reason });
    if (!r.ok) return toastError(r.msg || "Falha ao alterar status");
    toastOk(r.msg || "OK");
    table.ajax.reload(null, false);
  });

  $("#usersTable").on("click", ".btnReset", async function () {
    const id = Number(this.dataset.id);
    const c = await confirmWithReason({
      title: "Resetar senha?",
      confirmText: "Resetar",
      placeholder: "Motivo (obrigatório)"
    });
    if (!c.confirmed) return;

    const { value: newPass } = await Swal.fire({
      title: "Nova senha",
      input: "text",
      inputPlaceholder: "Digite a nova senha",
      showCancelButton: true,
      confirmButtonText: "Aplicar"
    });
    if (!newPass) return;

    const r = await apiPost("/api/users/reset-password", { id, new_password: newPass, reason: c.reason });
    if (!r.ok) return toastError(r.msg || "Falha ao resetar");
    toastOk("Senha alterada.");
  });
});
