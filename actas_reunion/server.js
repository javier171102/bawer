const express = require('express');
const nodemailer = require('nodemailer');
const bodyParser = require('body-parser');
const cors = require('cors');
const mysql = require('mysql2');
const path = require('path');
const app = express();
const PORT = process.env.PORT || 10000;
// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
// Servir archivos estáticos
app.use(express.static(path.join(__dirname)));
// Transportador de nodemailer
const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
        user: 'wajafees@gmail.com',
        pass: 'rlfs dyip dlnq igbo'
    }
});
// Conexión a la base de datos MySQL
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'notaria_db'
});
// Conectar a la base de datos
connection.connect((err) => {
    if (err) {
        console.error('Error conectando a la base de datos: ' + err.stack);
        return;
    }
    console.log('Conectado a la base de datos MySQL.');
});
// Endpoint para enviar el correo y guardar en la base de datos
app.post('/send-email', (req, res) => {
    const { fecha, hora, lugar, tema, observaciones, asistentes } = req.body;
    // Verificación para asegurarse de que `fecha` no sea null
    if (!fecha) {
        return res.status(400).send('El campo fecha es obligatorio.');
    }
    // Mensaje del correo
    const message = `Reunión: ${tema}\nFecha: ${fecha}\nHora: ${hora}\nLugar: ${lugar}\nObservaciones: ${observaciones}\nAsistentes: ${asistentes}`;
    // Envío de correo electrónico
    const mailOptions = {
        from: 'wajafees@gmail.com',
        to: asistentes,
        subject: 'Acta de Reunión',
        text: message,
    };
    // Enviar el correo
    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            return res.status(500).send(error.toString());
        }
        // Insertar datos en la base de datos
        const query = 'INSERT INTO reuniones (fecha, hora, lugar, tema, observaciones, asistentes) VALUES (?, ?, ?, ?, ?, ?)';
        connection.query(query, [fecha, hora, lugar, tema, observaciones, asistentes], (err, _results) => {
            if (err) {
                return res.status(500).send('Error al guardar en la base de datos: ' + err.message);
            }
            res.status(200).send('Correo enviado y datos guardados correctamente: ' + info.response);
        });
    });
});
// Endpoint para consultar documentos usando el DNI
app.get('/api/consultar-documento', (req, res) => {
    const { dni } = req.query;
    if (!dni) {
        return res.status(400).send('El campo DNI es obligatorio para realizar la consulta.');
    }
    const results = [];
    // Consultar en testamentos
    const testamentosQuery = `
        SELECT 'testamentos' AS origen, nombre, dni, tipo_testamento AS tipo, testamento
        FROM testamentos
        WHERE dni = ?
    `;
    connection.query(testamentosQuery, [dni], (err, testamentosResultados) => {
        if (err) {
            return res.status(500).send('Error al consultar testamentos: ' + err.message);
        }
        results.push(...testamentosResultados);
        // Consultar en contratos
        const contratosQuery = `
            SELECT 'contratos' AS origen, nombre, dni, tipo_contrato AS tipo, documento
            FROM contratos
            WHERE dni = ?
        `;
        connection.query(contratosQuery, [dni], (err, contratosResultados) => {
            if (err) {
                return res.status(500).send('Error al consultar contratos: ' + err.message);
            }
            results.push(...contratosResultados);
            // Consultar en consultas
            const consultasQuery = `
                SELECT 'consultas' AS origen, nombre, dni, consulta AS tipo, consulta AS consulta_documento
                FROM consultas
                WHERE dni = ?
            `;
            connection.query(consultasQuery, [dni], (err, consultasResultados) => {
                if (err) {
                    return res.status(500).send('Error al consultar consultas: ' + err.message);
                }
                results.push(...consultasResultados);
                // Enviar los resultados finales
                res.json(results);
            });
            async function guardarConsulta(dni, nombre, tipo, documento) {
                try {
                    const response = await fetch('/api/guardar-consulta', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ dni, nombre, tipo, documento }),
                    });
                    if (!response.ok) {
                        throw new Error('Error al guardar la consulta');
                    }
                    const result = await response.json();
                    console.log('Consulta guardada con ID:', result.id);
                } catch (error) {
                    console.error('Error al guardar la consulta:', error);
                }
            }
            if (resultados.length > 0) {
                // Mostrar resultados en la tabla...
                
                // Llamar a la función para guardar la consulta
                guardarConsulta(dni, resultados[0].nombre, resultados[0].tipo, resultados[0].documento);
            } else {
                alert('No se encontraron resultados para el DNI ingresado.');
            }
        });
    });
});
// Iniciar el servidor
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});