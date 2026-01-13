/**
 * Genera un enlace de WhatsApp con un mensaje formateado para una venta.
 * @param {Object} sale - Objeto con la información de la venta.
 * @param {string} sale.storeName - Nombre de la tienda.
 * @param {Array} sale.products - Lista de productos {name, quantity, price}.
 * @param {number} sale.total - Total de la venta.
 * @param {string} [phoneNumber] - (Opcional) Número de teléfono de la tienda (formato internacional, ej: 5215555555555).
 * @returns {string} URL de WhatsApp.
 */
function generateWhatsAppLink(sale, phoneNumber = "") {
    // Encabezado del mensaje
    let message = `Hola *${sale.storeName}*! 👋\n`;
    message += `Me gustaría realizar el siguiente pedido:\n\n`;

    // Listar productos
    sale.products.forEach(product => {
        const subtotal = (product.price * product.quantity).toFixed(2);
        message += `📦 *${product.name}* (x${product.quantity}) - $${subtotal}\n`;
    });

    // Total
    message += `\n💰 *Total: $${sale.total.toFixed(2)}*\n\n`;
    message += `Gracias!`;

    // Codificar el mensaje para URL
    const encodedMessage = encodeURIComponent(message);

    // Construir el link
    // Si hay número, usa wa.me/numero?text=... sino solo wa.me/?text=...
    const baseUrl = phoneNumber ? `https://wa.me/${phoneNumber}` : "https://wa.me/";
    
    return `${baseUrl}?text=${encodedMessage}`;
}

// Ejemplo de uso (para probar en consola o Node.js)
/*
const exampleSale = {
    storeName: "Tienda Unicornio",
    products: [
        { name: "Zapatillas Deportivas", quantity: 1, price: 85.00 },
        { name: "Calcetines Pack x3", quantity: 2, price: 12.50 }
    ],
    total: 110.00
};

console.log(generateWhatsAppLink(exampleSale));
*/
