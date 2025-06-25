const token = 'VYDCUuUzLhDCKknVLCjSrdGObRGeyLVDexwPyXgH';

// Função para pegar o valor do parâmetro 'busca' da URL
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

const container = document.getElementById('discos');
const query = getQueryParam('busca');

if (!query) {
  container.innerHTML = 'Nenhuma busca realizada.';
} else {
  fetch(`https://api.discogs.com/database/search?q=${encodeURIComponent(query)}&type=release&token=${token}`)
    .then(res => res.json())
    .then(data => {
      container.innerHTML = '';

      if (data.results.length === 0) {
        container.innerHTML = 'Nenhum resultado encontrado.';
        return;
      }

      data.results.forEach(disco => {
        const div = document.createElement('a');
        div.href = `detalhesprod.php?id=${disco.id}`; // Redireciona para a página do produto
        div.classList.add('disco');
        div.innerHTML = `
          <img src="${disco.cover_image}" alt="Capa do Disco">
          <div>
            <strong>${disco.title}</strong><br>
            <small>${disco.year || 'Ano desconhecido'}</small>
          </div>
        `;
        container.appendChild(div);
      });
    })
    .catch(err => {
      container.innerHTML = 'Erro ao buscar os discos!';
      console.error(err);
    });
}