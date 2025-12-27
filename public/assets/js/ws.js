export function createWsClient({ url, onMessage, onOpen, onClose }) {
  const ws = new WebSocket(url);

  ws.onopen = () => onOpen?.();
  ws.onclose = () => onClose?.();
  ws.onmessage = (ev) => {
    try {
      const data = JSON.parse(ev.data);
      onMessage?.(data);
    } catch (err) {
      console.error('Mensagem WS inválida', err);
    }
  };

  return ws;
}
