document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('consulta-form');
    const salirBtn = document.getElementById('salir-btn');
    const resultadosContainer = document.getElementById('resultados-container');
    const documentosLista = document.getElementById('documentos-lista');

    // Función para consultar documentos a través de la API
    async function consultarDocumentos(dni) {
        try {
            const response = await fetch(`http://localhost:10000/api/consultar-documento?dni=${dni}`);
            if (!response.ok) {
                throw new Error('Error en la consulta a la API');
            }
            const resultados = await response.json();
            console.log('Resultados obtenidos:', resultados);
            return resultados;
        } catch (error) {
            console.error('Error:', error);
            alert('No se pudo realizar la consulta. Inténtalo de nuevo más tarde.');
            return [];
        }
    }

    // Manejador del botón de Salir
    salirBtn.addEventListener('click', function () {
        window.location.href = 'panel.html'; // Redirige a otra página
    });

    // Manejador del formulario
    form.addEventListener('submit', async function (event) {
        event.preventDefault(); // Evita el envío del formulario por defecto
        const dni = document.getElementById('consulta-dni').value.trim();
        
        // Validación del DNI
        if (!dni) {
            alert('Por favor, ingrese su número de DNI.');
            return;
        }
        if (!/^\d+$/.test(dni)) { // Validar que el DNI contenga solo números
            alert('El DNI debe contener solo números.');
            return;
        }

        // Mensaje de carga
        resultadosContainer.style.display = 'none'; // Ocultar resultados si se mostraban
        const loadingMessage = document.createElement('p');
        loadingMessage.textContent = 'Cargando, por favor espere...';
        resultadosContainer.appendChild(loadingMessage);

        // Realiza la consulta por el DNI y espera los resultados
        const resultados = await consultarDocumentos(dni);
        // Eliminar mensaje de carga
        resultadosContainer.removeChild(loadingMessage);

        if (resultados.length > 0) {
            resultadosContainer.style.display = 'block'; // Mostrar el contenedor de resultados
            documentosLista.innerHTML = ''; // Limpiar el contenido de la tabla
            
            // Crear un DocumentFragment para optimizar el DOM
            const fragment = document.createDocumentFragment();

            resultados.forEach(resultado => {
                const row = document.createElement('tr');
                const documentoLink = resultado.documento
                    ? `<a href="${resultado.documento}" target="_blank">Ver Documento</a>`
                    : 'No disponible';

                row.innerHTML = `
                    <td>${resultado.origen}</td>
                    <td>${resultado.nombre.trim()}</td>
                    <td>${resultado.dni}</td>
                    <td>${resultado.tipo}</td>
                    <td>${documentoLink}</td>
                `;
                fragment.appendChild(row); // Añadir la fila al fragmento
            });

            documentosLista.appendChild(fragment); // Añadir todas las filas al DOM en una sola operación
        } else {
            alert('No se encontraron resultados para el DNI ingresado.');
        }
    });
});
