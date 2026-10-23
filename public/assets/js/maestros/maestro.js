history.pushState(null, null, location.href);

    window.onpopstate = function() {
        history.go(1);
    };
    class Maestro {
        constructor(nombre, apellidoPaterno, apellidoMaterno, correo, materia, turno) {
            this.nombre = nombre;
            this.apellidoPaterno = apellidoPaterno;
            this.apellidoMaterno = apellidoMaterno;
            this.correo = correo;
            this.materia = materia;
            this.turno = turno;
        }

        obtenerNombreCompleto() {
            return `${this.nombre} ${this.apellidoPaterno} ${this.apellidoMaterno}`;
        }
    }

    class GestionMaestros {
        constructor() {
            this.maestros = [];
            this.botonAgregar = document.getElementById("btnAgregarMaestro");
            this.tabla = document.getElementById("tablaMaestros");
        }

        iniciar() {
            this.botonAgregar.addEventListener("click", () => {
                this.agregarMaestro();
            });
        }

        validarCampos(nombre, apellidoPaterno, correo, materia, turno) {
            const correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);

            return nombre.length >= 3 &&
                apellidoPaterno.length >= 3 &&
                correoValido &&
                materia.length >= 3 &&
                turno !== "";
        }

        agregarMaestro() {
            const nombre = document.getElementById("nombre").value.trim();
            const apellidoPaterno = document.getElementById("apellido_paterno").value.trim();
            const apellidoMaterno = document.getElementById("apellido_materno").value.trim();
            const correo = document.getElementById("correo").value.trim();
            const materia = document.getElementById("materia").value.trim();
            const turno = document.getElementById("turno").value;

            if (!this.validarCampos(nombre, apellidoPaterno, correo, materia, turno)) {
                alert("Verifica que los campos estén completos y que el correo sea válido.");
                return;
            }

            const maestro = new Maestro(
                nombre,
                apellidoPaterno,
                apellidoMaterno,
                correo,
                materia,
                turno
            );

            this.maestros.push(maestro);

            this.mostrarMaestros();
            this.limpiarFormulario();
            this.mostrarMensajeAsincrono();
        }

        mostrarMaestros() {
            this.tabla.innerHTML = "";

            this.maestros.forEach(maestro => {
                const fila = document.createElement("tr");

                const columnaNombre = document.createElement("td");
                const columnaCorreo = document.createElement("td");
                const columnaMateria = document.createElement("td");
                const columnaTurno = document.createElement("td");

                columnaNombre.textContent = maestro.obtenerNombreCompleto();
                columnaCorreo.textContent = maestro.correo;
                columnaMateria.textContent = maestro.materia;
                columnaTurno.textContent = maestro.turno;

                fila.appendChild(columnaNombre);
                fila.appendChild(columnaCorreo);
                fila.appendChild(columnaMateria);
                fila.appendChild(columnaTurno);

                fila.classList.add("fila-animada");
                this.tabla.appendChild(fila);
            });
        }

        limpiarFormulario() {
            document.getElementById("nombre").value = "";
            document.getElementById("apellido_paterno").value = "";
            document.getElementById("apellido_materno").value = "";
            document.getElementById("correo").value = "";
            document.getElementById("materia").value = "";
            document.getElementById("turno").value = "";
        }

        mostrarMensajeAsincrono() {
            setTimeout(() => {
                alert("Maestro agregado correctamente.");
            }, 800);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const gestion = new GestionMaestros();
        gestion.iniciar();
    });