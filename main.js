// Chatbot functionality
const chatToggle = document.getElementById('chat-toggle');
const chatbot = document.getElementById('chatbot');
const closeChat = document.getElementById('close-chat');
const chatMessages = document.getElementById('chat-messages');
const userInput = document.getElementById('user-input');
const sendMessage = document.getElementById('send-message');

// Simple responses for the chatbot
const botResponses = {
    'hello': 'Hi there! How can I help you with environmental concerns?',
    'water pollution': 'Water pollution is a serious issue. Some ways to help: reduce plastic use, proper waste disposal, and support clean water initiatives.',
    'air pollution': 'Air pollution affects us all. You can help by: using public transport, reducing energy consumption, and supporting clean energy.',
    'trees': 'Trees are essential for our planet! They absorb CO2, provide oxygen, and support biodiversity.',
    'help': 'I can provide information about environmental issues and ways to help. Try asking about water pollution, air pollution, or trees!',
};

// Toggle chatbot visibility
chatToggle.addEventListener('click', () => {
    chatbot.style.display = 'block';
    chatToggle.style.display = 'none';
});

closeChat.addEventListener('click', () => {
    chatbot.style.display = 'none';
    chatToggle.style.display = 'block';
});

// Send message function
function sendUserMessage() {
    const message = userInput.value.trim().toLowerCase();
    if (message) {
        // Add user message
        addMessage(message, 'user');
        
        // Get bot response
        let response = 'I\'m not sure about that. Try asking about water pollution, air pollution, or trees!';
        for (const [key, value] of Object.entries(botResponses)) {
            if (message.includes(key)) {
                response = value;
                break;
            }
        }
        
        // Add bot response after a short delay
        setTimeout(() => {
            addMessage(response, 'bot');
        }, 500);
        
        userInput.value = '';
    }
}

// Add message to chat
function addMessage(message, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', sender);
    messageDiv.textContent = message;
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Event listeners for sending messages
sendMessage.addEventListener('click', sendUserMessage);
userInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        sendUserMessage();
    }
});

// Slider functionality
let currentSlide = 0;
const slides = [
    {
        title: 'Save Our Planet',
        text: 'Together we can make a difference'
    },
    {
        title: 'Protect Our Waters',
        text: 'Every drop counts'
    },
    {
        title: 'Clean Air for All',
        text: 'Breathe the change you wish to see'
    }
];

function updateSlide() {
    const slideContent = document.querySelector('.slide-content');
    slideContent.innerHTML = `
        <h1>${slides[currentSlide].title}</h1>
        <p>${slides[currentSlide].text}</p>
    `;
    
    currentSlide = (currentSlide + 1) % slides.length;
}

// Change slide every 5 seconds
setInterval(updateSlide, 5000);

// Initialize learn more buttons
document.querySelectorAll('.learn-more').forEach(button => {
    button.addEventListener('click', (e) => {
        const topic = e.target.parentElement.querySelector('h2').textContent;
        alert(`Learn more about ${topic}`);
    });
});

// Initialize Vanasaya button
document.querySelector('.vanasaya-btn').addEventListener('click', () => {
    alert('Join our tree planting initiative!');
});