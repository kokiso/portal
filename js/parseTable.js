/**
 * converte array-como objeto para array
 * @param  coleção o objeto a ser convertido
 * @return {Array} o objeto convertido
 */
function arrayify(collection) {
  return Array.prototype.slice.call(collection);
}
/**
 * gera funções de fábrica para converter linhas de tabela em objetos,
 * com base nos títulos da table's <thead>
 * @param  {Array[String]} títulos os valores da table's <thead>
 * @return {Function}      uma função que pega uma linha da tabela e cospe um objeto
 */
function factory(headings) {
  return function(row) {
    return arrayify(row.cells).reduce(function(prev, curr, i) {
      prev[headings[i]] = curr.innerText;
      return prev;
    }, {});
  }
}
/**
 * dada uma tabela, gere uma matriz de objetos.
 * cada objeto corresponde a uma linha na tabela.
 * cada objeto key/value pares correspondem ao cabeçalho de uma coluna e ao valor da linha para essa coluna
 * 
 * @param  {HTMLTableElement} table the table para converter
 * @return {Array[Object]}       array de objetos representando cada linha na tabela
 */
function parseTable(table) {
  var headings = arrayify(table.tHead.rows[0].cells).map(function(heading) {
    return heading.innerText;
  });
  return arrayify(table.tBodies[0].rows).map(factory(headings));
}