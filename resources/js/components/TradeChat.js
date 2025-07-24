import React, { useState, useEffect, useRef } from 'react';
import { Send, Image, XCircle } from 'lucide-react'; // Importa iconos de lucide-react

// Asegúrate de que window.Echo y window.axios estén configurados en tu bootstrap.js
// y que Laravel Echo esté disponible globalmente.

// Componente principal de la aplicación
const App = () => {
  // Estado para almacenar los detalles de la transacción
  const [trade, setTrade] = useState(null);
  // Estado para almacenar los mensajes del chat
  const [messages, setMessages] = useState([]);
  // Estado para el contenido del nuevo mensaje de texto
  const [newMessageContent, setNewMessageContent] = useState('');
  // Estado para el archivo de imagen a adjuntar
  const [imageFile, setImageFile] = useState(null);
  // Estado para el ID de la transacción (simulado, en una app real vendría de la URL o props)
  const [tradeId, setTradeId] = useState(1); // ¡IMPORTANTE: Reemplaza con el ID de transacción real!
  // Estado para manejar el estado de carga
  const [loading, setLoading] = useState(true);
  // Estado para manejar errores
  const [error, setError] = useState(null);
  // Referencia para el scroll automático del chat
  const messagesEndRef = useRef(null);

  // Función para desplazar el chat hacia abajo automáticamente
  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  // Efecto para cargar los detalles de la transacción y los mensajes al inicio
  useEffect(() => {
    const fetchTradeAndMessages = async () => {
      setLoading(true);
      setError(null);
      try {
        // Simula la obtención del token de autenticación (Sanctum)
        // En una aplicación real, esto se manejaría con un contexto de autenticación o similar.
        // Para pruebas locales, asegúrate de que tu sesión de Laravel esté activa.
        
        // Obtener detalles de la transacción
        const tradeResponse = await axios.get(`/api/trades/${tradeId}`);
        setTrade(tradeResponse.data.trade);

        // Obtener historial de mensajes
        const messagesResponse = await axios.get(`/api/trades/${tradeId}/messages`);
        setMessages(messagesResponse.data.messages);

      } catch (err) {
        console.error('Error al cargar datos:', err);
        setError('Error al cargar la transacción o los mensajes. Asegúrate de que el ID de la transacción es válido y estás autenticado.');
      } finally {
        setLoading(false);
      }
    };

    fetchTradeAndMessages();
  }, [tradeId]); // Recarga si el ID de la transacción cambia

  // Efecto para suscribirse a los canales de Pusher
  useEffect(() => {
    if (tradeId && window.Echo) {
      // Suscribirse al canal de chat de la transacción
      window.Echo.channel(`trade.chat.${tradeId}`)
        .listen('.new.message', (e) => {
          console.log('Nuevo mensaje recibido en tiempo real:', e);
          // Añadir el nuevo mensaje al estado
          setMessages((prevMessages) => [...prevMessages, e]);
        })
        .error((error) => {
          console.error('Error al escuchar el canal de chat:', error);
          setError('Error en la conexión en tiempo real del chat.');
        });

      // Desuscribirse del canal cuando el componente se desmonte o el tradeId cambie
      return () => {
        window.Echo.leave(`trade.chat.${tradeId}`);
        console.log(`Dejando el canal trade.chat.${tradeId}`);
      };
    }
  }, [tradeId]); // Resuscribirse si el ID de la transacción cambia

  // Efecto para hacer scroll al final del chat cuando se añaden nuevos mensajes
  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  // Manejar el envío de un nuevo mensaje
  const handleSendMessage = async (e) => {
    e.preventDefault();

    if (!newMessageContent.trim() && !imageFile) {
      alert('Por favor, escribe un mensaje o adjunta una imagen.'); // Usar modal en producción
      return;
    }

    const formData = new FormData();
    if (newMessageContent.trim()) {
      formData.append('content', newMessageContent.trim());
    }
    if (imageFile) {
      formData.append('image', imageFile);
    }

    try {
      // Enviar el mensaje/imagen al backend
      const response = await axios.post(`/api/trades/${tradeId}/messages`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data', // Importante para enviar archivos
        },
      });
      console.log('Mensaje enviado:', response.data);
      // Añadir el mensaje enviado al estado (el broadcast también lo hará, pero esto es inmediato)
      setMessages((prevMessages) => [...prevMessages, response.data.chat_message]);
      setNewMessageContent(''); // Limpiar el campo de texto
      setImageFile(null); // Limpiar el archivo de imagen
    } catch (err) {
      console.error('Error al enviar mensaje:', err);
      setError('Error al enviar el mensaje. Inténtalo de nuevo.');
    }
  };

  // Manejar la selección de imagen
  const handleImageChange = (e) => {
    if (e.target.files[0]) {
      setImageFile(e.target.files[0]);
    }
  };

  // Manejar la eliminación de la imagen seleccionada
  const handleRemoveImage = () => {
    setImageFile(null);
    document.getElementById('image-upload').value = null; // Limpiar el input file
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-gray-100">
        <p className="text-lg text-gray-700">Cargando transacción y chat...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-red-100 text-red-800 p-4 rounded-lg">
        <p className="text-lg">{error}</p>
      </div>
    );
  }

  if (!trade) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-yellow-100 text-yellow-800 p-4 rounded-lg">
        <p className="text-lg">No se encontró la transacción con el ID {tradeId}.</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-100 flex flex-col items-center p-4 font-inter">
      <div className="w-full max-w-3xl bg-white rounded-lg shadow-xl overflow-hidden">
        {/* Encabezado de la Transacción */}
        <div className="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white rounded-t-lg">
          <h1 className="text-3xl font-bold mb-2">Transacción P2P #{trade.id}</h1>
          <p className="text-lg">Estado: <span className={`font-semibold ${trade.status === 'accepted' ? 'text-green-200' : 'text-yellow-200'}`}>{trade.status.toUpperCase()}</span></p>
          <p className="text-md">Monto: <span className="font-semibold">{trade.amount} {trade.cryptocurrency}</span></p>
          <p className="text-md">Tipo: <span className="font-semibold">{trade.type === 'buy' ? 'Compra' : 'Venta'}</span></p>
        </div>

        {/* Área de Mensajes del Chat */}
        <div className="p-6 h-96 overflow-y-auto bg-gray-50 border-b border-gray-200">
          {messages.length === 0 ? (
            <p className="text-center text-gray-500 mt-20">No hay mensajes en este chat aún.</p>
          ) : (
            messages.map((msg) => (
              <div
                key={msg.id}
                className={`flex mb-4 ${msg.user_id === Auth.user.id ? 'justify-end' : 'justify-start'}`}
              >
                <div
                  className={`max-w-[70%] p-3 rounded-lg shadow-md ${
                    msg.user_id === Auth.user.id
                      ? 'bg-blue-500 text-white rounded-br-none'
                      : 'bg-gray-200 text-gray-800 rounded-bl-none'
                  }`}
                >
                  <p className="font-semibold text-sm mb-1">{msg.user_name}</p>
                  {msg.content && <p className="text-sm">{msg.content}</p>}
                  {msg.image_url && (
                    <div className="mt-2">
                      <img
                        src={msg.image_url}
                        alt="Comprobante de pago"
                        className="max-w-full h-auto rounded-md border border-gray-300"
                        onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/150x100/CCCCCC/333333?text=Imagen+no+disponible"; }}
                      />
                      <p className="text-xs mt-1 text-right italic">Comprobante</p>
                    </div>
                  )}
                  <p className={`text-xs mt-1 ${msg.user_id === Auth.user.id ? 'text-blue-200' : 'text-gray-500'} text-right`}>
                    {msg.created_at}
                  </p>
                </div>
              </div>
            ))
          )}
          <div ref={messagesEndRef} /> {/* Elemento para el scroll automático */}
        </div>

        {/* Formulario de Envío de Mensajes */}
        <form onSubmit={handleSendMessage} className="p-6 bg-gray-100 flex flex-col gap-4">
          {imageFile && (
            <div className="flex items-center justify-between p-3 bg-blue-100 rounded-md border border-blue-300">
              <span className="text-blue-800 text-sm">Imagen adjunta: {imageFile.name}</span>
              <button
                type="button"
                onClick={handleRemoveImage}
                className="text-blue-600 hover:text-blue-800 focus:outline-none"
                aria-label="Eliminar imagen"
              >
                <XCircle size={20} />
              </button>
            </div>
          )}
          <div className="flex items-center gap-3">
            <input
              type="text"
              value={newMessageContent}
              onChange={(e) => setNewMessageContent(e.target.value)}
              placeholder="Escribe tu mensaje..."
              className="flex-grow p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
              disabled={loading}
            />
            <label htmlFor="image-upload" className="cursor-pointer p-3 bg-gray-300 rounded-md hover:bg-gray-400 transition-colors duration-200" title="Adjuntar imagen">
              <input
                id="image-upload"
                type="file"
                accept="image/*"
                onChange={handleImageChange}
                className="hidden"
                disabled={loading}
              />
              <Image size={24} className="text-gray-700" />
            </label>
            <button
              type="submit"
              className="p-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2 font-semibold"
              disabled={loading}
            >
              <Send size={20} />
              Enviar
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default App;
