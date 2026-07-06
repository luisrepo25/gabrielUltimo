(function () {
    'use strict';

    function byId(id) {
        return document.getElementById(id);
    }

    function toggleForm(tipo) {
        const estudio = byId('container-estudio');
        const modificar = byId('container-modificar');
        const eliminar = byId('container-eliminar');

        if (!estudio || !modificar || !eliminar) return;

        estudio.classList.add('d-none');
        modificar.classList.add('d-none');
        eliminar.classList.add('d-none');

        document.querySelectorAll('.btn-action').forEach(function (b) {
            b.classList.remove('active');
        });

        const container = byId('container-' + tipo);
        const btn = byId('btn-toggle-' + tipo);
        if (container) container.classList.remove('d-none');
        if (btn) btn.classList.add('active');
    }

    // Exponer por compatibilidad con onclick en la vista
    window.toggleForm = toggleForm;

    function initToggleButtons() {
        const btnEstudio = byId('btn-toggle-estudio');
        const btnModificar = byId('btn-toggle-modificar');
        const btnEliminar = byId('btn-toggle-eliminar');

        if (btnEstudio) btnEstudio.addEventListener('click', function () { toggleForm('estudio'); });
        if (btnModificar) btnModificar.addEventListener('click', function () { toggleForm('modificar'); });
        if (btnEliminar) btnEliminar.addEventListener('click', function () { toggleForm('eliminar'); });
    }

    function initEliminarConfirm() {
        const formEliminar = byId('form-eliminar');
        if (!formEliminar) return;

        formEliminar.addEventListener('submit', function (e) {
            const ok = window.confirm('¿Confirmas borrar esta alma tanto del Motor de Tienda como del Motor Auth?');
            if (!ok) e.preventDefault();
        });
    }

    function fetchUsuario(ci) {
        return fetch('/api/usuario/' + encodeURIComponent(ci)).then(function (res) {
            return res.json();
        });
    }

    function initEstudio() {
        const ciEstudio = byId('ci-estudio');
        if (!ciEstudio) return;

        ciEstudio.addEventListener('input', function () {
            clearTimeout(window.estudioTimeout);

            const errorMsg = byId('estudio-error-msg');
            const studyBox = byId('study-results');

            if (errorMsg) errorMsg.classList.add('d-none');
            if (studyBox) studyBox.classList.add('d-none');

            const ci = this.value;
            if (!ci) return;

            window.estudioTimeout = setTimeout(function () {
                fetchUsuario(ci).then(function (data) {
                    if (data && data.success) {
                        const u = data.usuario;
                        byId('study-name').textContent = u.nombre + ' ' + (u.apellido || '');
                        byId('study-mail').textContent = u.correo;
                        byId('study-tipo').textContent = u.tipoPersona;
                        byId('study-categoria').textContent = (u.cliente && u.cliente.categoria) ? u.cliente.categoria : (u.tipoPersona === 'C' ? 'Sin categoría' : 'N/A');

                        const taskList = byId('study-tasks');
                        taskList.textContent = '';

                        const asignaciones = data.detalles_estudio_asignaciones;
                        if (Array.isArray(asignaciones) && asignaciones.length > 0) {
                            asignaciones.forEach(function (asig) {
                                const li = document.createElement('li');

                                const strong = document.createElement('strong');
                                strong.textContent = ((asig.rol && asig.rol.nombre) ? asig.rol.nombre : 'Rol') + ':';
                                li.appendChild(strong);

                                li.appendChild(document.createTextNode(' Iniciado ' + (asig.fechaInicio || '') + '. '));

                                const badge = document.createElement('span');
                                badge.className = 'badge';
                                badge.textContent = asig.estado || '';
                                li.appendChild(badge);

                                taskList.appendChild(li);
                            });
                        } else {
                            const li = document.createElement('li');
                            li.style.borderLeft = '3px solid #cbd5e1';
                            li.style.color = '#64748b';
                            li.textContent = 'El sistema indica que este usuario no tiene historial de labores asignadas.';
                            taskList.appendChild(li);
                        }

                        if (studyBox) studyBox.classList.remove('d-none');
                    } else if (errorMsg) {
                        errorMsg.textContent = 'Individuo no hallado en la base de datos empresarial.';
                        errorMsg.classList.remove('d-none');
                    }
                });
            }, 500);
        });
    }

    function initModificar() {
        const ciModificar = byId('ci-modificar');
        if (!ciModificar) return;

        ciModificar.addEventListener('input', function () {
            clearTimeout(window.modTimeout);

            const errorMsg = byId('modificar-error-msg');
            if (errorMsg) errorMsg.classList.add('d-none');

            const ci = this.value;
            if (!ci) return;

            window.modTimeout = setTimeout(function () {
                fetchUsuario(ci)
                    .then(function (data) {
                        if (data && data.success) {
                            const u = data.usuario;
                            byId('modnombre').value = u.nombre;
                            byId('modapellido').value = u.apellido || '';
                            byId('modtelefono').value = u.telefono || '';
                            byId('modsexo').value = u.sexo || 'M';
                            byId('modcorreo').value = u.correo;
                            byId('moddomicilio').value = u.domicilio || '';
                            byId('modtipo').value = u.tipoPersona || '';
                            byId('modcategoria').value = (u.cliente && u.cliente.categoria) ? u.cliente.categoria : '';

                            const form = byId('form-modificar');
                            if (form) form.action = '/usuarios/' + encodeURIComponent(ci);
                        } else if (errorMsg) {
                            errorMsg.textContent = 'Ese carnet no está en las listas.';
                            errorMsg.classList.remove('d-none');
                        }
                    })
                    .catch(function (err) {
                        console.error('Fetch error:', err);
                        if (!errorMsg) return;
                        errorMsg.textContent = 'Error de conexión o datos no válidos.';
                        errorMsg.classList.remove('d-none');
                    });
            }, 500);
        });
    }

    function initEliminar() {
        const ciEliminar = byId('ci-eliminar');
        if (!ciEliminar) return;

        ciEliminar.addEventListener('input', function () {
            clearTimeout(window.delTimeout);

            const errorMsg = byId('eliminar-error-msg');
            if (errorMsg) errorMsg.classList.add('d-none');

            const ci = this.value;
            const delnombre = byId('delnombre');

            if (!ci) {
                if (delnombre) delnombre.value = '';
                return;
            }

            window.delTimeout = setTimeout(function () {
                fetchUsuario(ci).then(function (data) {
                    const form = byId('form-eliminar');

                    if (data && data.success) {
                        const u = data.usuario;
                        if (delnombre) delnombre.value = 'Objetivo Activo: ' + u.nombre + ' ' + u.correo;
                        if (form) form.action = '/usuarios/' + encodeURIComponent(ci);
                    } else {
                        if (errorMsg) {
                            errorMsg.textContent = 'Persona fantasma.';
                            errorMsg.classList.remove('d-none');
                        }
                        if (delnombre) delnombre.value = '';
                    }
                });
            }, 500);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initToggleButtons();
        initEstudio();
        initModificar();
        initEliminar();
        initEliminarConfirm();
    });
})();
