import { apiPost } from "../api.js";
import { toastError, toastOk } from "../ui.js";

function formToObj(form) {
  const fd = new FormData(form);
  const o = {};
  for (const [k, v] of fd.entries()) o[k] = v;
  o.ativo = form.querySelector('[name="ativo"]').checked ? 1 : 0;
  o.must_change_password = form.querySelector('[name="must_change_password"]').checked ? 1 : 0;
  if (o.id) o.id = Number(o.id);
  if (o.role_id) o.role_id = Number(o.role_id);
  return o;
}

document.getElementById("btnSave")?.addEventListener("click", async () => {
  const form = document.getElementById("userForm");
  const data = formToObj(form);

  const url = data.id && data.id > 0 ? "/api/users/update" : "/api/users/create";
  const r = await apiPost(url, data);
  if (!r.ok) return toastError(r.msg || "Falha ao salvar");

  toastOk(r.msg || "Salvo com sucesso");
  window.location.href = "/usuarios";
});
