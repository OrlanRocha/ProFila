export async function apiGet(url) {
  const res = await fetch(url, { credentials: "include" });
  const data = await res.json().catch(() => ({ ok:false, msg:"Resposta inválida" }));
  if (!res.ok && data?.msg) return data;
  return data;
}

export async function apiPost(url, body) {
  const res = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF": (window.PROFILA?.csrf || "")
    },
    credentials: "include",
    body: JSON.stringify(body ?? {})
  });
  const data = await res.json().catch(() => ({ ok:false, msg:"Resposta inválida" }));
  if (!res.ok && data?.msg) return data;
  return data;
}
