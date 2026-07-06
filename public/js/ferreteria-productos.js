(function () {
    'use strict';

    function byId(id) {
        return document.getElementById(id);
    }

    function toggleForm(tipo) {
        const containerAgregar = byId('container-agregar');
        const containerModificar = byId('container-modificar');
        const containerEliminar = byId('container-eliminar');
        const btnAgregar = byId('btn-toggle-agregar');
        const btnModificar = byId('btn-toggle-modificar');
        const btnEliminar = byId('btn-toggle-eliminar');

        if (!containerAgregar || !containerModificar || !containerEliminar || !btnAgregar || !btnModificar || !btnEliminar) {
            return;
        }

        containerAgregar.classList.add('d-none');
        containerModificar.classList.add('d-none');
        containerEliminar.classList.add('d-none');

        btnAgregar.classList.remove('active');
        btnModificar.classList.remove('active');
        btnEliminar.classList.remove('active');

        if (tipo !== 'ninguno' && tipo) {
            const container = byId('container-' + tipo);
            const btn = byId('btn-toggle-' + tipo);
            if (container) container.classList.remove('d-none');
            if (btn) btn.classList.add('active');
        }
    }

    // Exponer por compatibilidad con onclick en la vista
    window.toggleForm = toggleForm;

    function initToggleButtons() {
        const btnAgregar = byId('btn-toggle-agregar');
        const btnModificar = byId('btn-toggle-modificar');
        const btnEliminar = byId('btn-toggle-eliminar');

        if (btnAgregar) btnAgregar.addEventListener('click', function () { toggleForm('agregar'); });
        if (btnModificar) btnModificar.addEventListener('click', function () { toggleForm('modificar'); });
        if (btnEliminar) btnEliminar.addEventListener('click', function () { toggleForm('eliminar'); });
    }

    function initEliminarConfirm() {
        const formEliminar = byId('form-eliminar');
        if (!formEliminar) return;

        formEliminar.addEventListener('submit', function (e) {
            const ok = window.confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.');
            if (!ok) e.preventDefault();
        });
    }

    function initInitialForm() {
        const el = document.querySelector('[data-initial-form]');
        const initialForm = el && el.dataset ? el.dataset.initialForm : null;
        if (initialForm) {
            toggleForm(initialForm);
        }
    }

    function initModificarAutofill() {
        const idModificar = byId('idproducto-modificar');
        if (!idModificar) return;

        idModificar.addEventListener('input', function () {
            clearTimeout(window.modificarTimeout);

            const errorMsg = byId('modificar-error-msg');
            if (errorMsg) {
                errorMsg.classList.add('d-none');
                errorMsg.textContent = '';
            }

            const id = this.value;
            if (!id) return;

            window.modificarTimeout = setTimeout(function () {
                fetch('/api/producto/' + encodeURIComponent(id))
                    .then(function (res) {
                        return res.json();
                    })
                    .then(function (data) {
                        if (data && data.success) {
                            const p = data.producto;
                            byId('modnombre').value = p.nombre;
                            byId('moddescripcion').value = p.descripcion || '';
                            byId('modprecio').value = p.precio;
                            byId('modcantidad').value = p.cantidad;
                            byId('modmarca').value = p.id_marca;
                            byId('modcategoria').value = p.id_categoria;
                            byId('modfechacaducidad').value = p.fechacaducidad || '';
                            byId('modcolor').value = p.id_color || '';
                            byId('modmedida').value = p.id_medida || '';
                            byId('modvolumen').value = p.id_volumen || '';

                            const form = byId('form-modificar');
                            if (form) form.action = '/productos/' + encodeURIComponent(id) + '?modificar=1';
                        } else if (errorMsg) {
                            errorMsg.textContent = 'Ese producto no existe.';
                            errorMsg.classList.remove('d-none');
                        }
                    })
                    .catch(function () {
                        if (!errorMsg) return;
                        errorMsg.textContent = 'Error al buscar el producto.';
                        errorMsg.classList.remove('d-none');
                    });
            }, 500);
        });
    }

    function initEliminarAutofill() {
        const idEliminar = byId('idproducto-eliminar');
        if (!idEliminar) return;

        idEliminar.addEventListener('input', function () {
            clearTimeout(window.eliminarTimeout);

            const errorMsg = byId('eliminar-error-msg');
            if (errorMsg) {
                errorMsg.classList.add('d-none');
                errorMsg.textContent = '';
            }

            const id = this.value;
            if (!id) {
                const elnombre = byId('elnombre');
                const elprecio = byId('elprecio');
                if (elnombre) elnombre.value = '';
                if (elprecio) elprecio.value = '';
                return;
            }

            window.eliminarTimeout = setTimeout(function () {
                fetch('/api/producto/' + encodeURIComponent(id))
                    .then(function (res) {
                        return res.json();
                    })
                    .then(function (data) {
                        const elnombre = byId('elnombre');
                        const elprecio = byId('elprecio');
                        const form = byId('form-eliminar');

                        if (data && data.success) {
                            const p = data.producto;
                            if (elnombre) elnombre.value = p.nombre;
                            if (elprecio) elprecio.value = p.precio + ' Bs';
                            if (form) form.action = '/productos/' + encodeURIComponent(id) + '?eliminar=1';
                        } else {
                            if (errorMsg) {
                                errorMsg.textContent = 'Ese producto no existe.';
                                errorMsg.classList.remove('d-none');
                            }
                            if (elnombre) elnombre.value = '';
                            if (elprecio) elprecio.value = '';
                        }
                    })
                    .catch(function () {
                        if (!errorMsg) return;
                        errorMsg.textContent = 'Error al buscar el producto.';
                        errorMsg.classList.remove('d-none');
                    });
            }, 500);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initToggleButtons();
        initInitialForm();
        initModificarAutofill();
        initEliminarAutofill();
        initEliminarConfirm();
    });
})();
