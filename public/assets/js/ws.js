export function createWsClient({ url, onMessage, onOpen, onClose }) {
  const ws = new WebSocket(url);

  ws.onopen = () => onOpen?.(ws);
  ws.onclose = () => onClose?.(ws);
  ws.onmessage = (ev) => {
    try {
      const data = JSON.parse(ev.data);
      onMessage?.(data, ws);
    } catch (err) {
      console.error('Mensagem WS inválida', err);
    }
  };

  return ws;
}

export function connectWS({ url, rooms = [], onMessage, onOpen, onClose }) {
  const ws = createWsClient({
    url,
    onOpen: (sock) => {
      rooms.forEach((room) => {
        try {
          sock.send(JSON.stringify({ type: 'SUBSCRIBE', room }));
        } catch (e) {
          console.error('Falha ao inscrever WS', e);
        }
      });
      onOpen?.(sock);
    },
    onClose,
    onMessage,
  });

  return ws;
}
