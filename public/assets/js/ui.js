export function toastOk(msg) { toastr.success(msg || "OK"); }
export function toastError(msg) { toastr.error(msg || "Erro"); }

export async function confirmWithReason({ title, confirmText, placeholder }) {
  const r = await Swal.fire({
    title: title || "Confirmar?",
    input: "text",
    inputLabel: "Motivo",
    inputPlaceholder: placeholder || "Motivo (obrigatório)",
    inputValidator: (v) => (!v || v.trim().length < 3) ? "Informe um motivo (mín. 3 caracteres)" : undefined,
    showCancelButton: true,
    confirmButtonText: confirmText || "Confirmar",
    cancelButtonText: "Cancelar"
  });
  return { confirmed: !!r.isConfirmed, reason: (r.value || "").trim() };
}
