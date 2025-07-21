// Carregar usuários médicos para o select
function carregarUsuariosMedicos(selectId, selectedId) {
    var select = document.getElementById(selectId);
    if (!select) return;
    select.innerHTML = '<option value="">(Opcional) Vincular a um usuário médico</option>';
    fetch(base_url + 'medicos/usuarios-medicos-api')
        .then(function (response) { return response.json(); })
        .then(function (usuarios) {
            usuarios.forEach(function (usuario) {
                var nome = (usuario.first_name && usuario.last_name) ? usuario.nome + ' ' + usuario.last_name : usuario.username;
                var option = document.createElement('option');
                option.value = usuario.id;
                option.text = nome + (usuario.email ? ' (' + usuario.email + ')' : '');
                if (selectedId && parseInt(selectedId) === parseInt(usuario.id)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        });
}
