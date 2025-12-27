export function initDataTables(selector, options = {}) {
  if (!window.jQuery || !jQuery.fn.DataTable) {
    console.warn('DataTables não disponível');
    return null;
  }

  return jQuery(selector).DataTable({
    pageLength: 25,
    order: [[0, 'desc']],
    ...options,
  });
}
