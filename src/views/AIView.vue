<template>
    <div class="chat-container">
        <h1>AI Chat Interface</h1>

        <div class="chat-box" ref="chatBox">
            <!-- Display messages -->
            <div v-for="message in messages" :key="message.id" class="message">
                <div :class="['message-content', message.role]">
                    <strong>{{ message.role === 'user' ? 'You' : 'AI' }}:</strong>
                    <!-- Render message content as HTML if it is from AI -->
                    <div v-if="message.role === 'ai'" v-html="formatAIResponse(message.content)"></div>
                    <!-- Render message content as plain text if it is from the user -->
                    <div v-else>{{ message.content }}</div>
                </div>
            </div>
        </div>

        <!-- Chat input form -->
        <form @submit.prevent="sendMessage" class="chat-form">
            <input
                v-model="userInput"
                type="text"
                placeholder="Type your message..."
                required
            />
            <button type="submit">Send</button>
        </form>
    </div>
</template>

<script>
export default {
    name: 'AIView',
    data() {
        return {
            userInput: '', // User input from the form
            messages: [], // Array to store messages for display
        };
    },
    methods: {
        async sendMessage() {
            if (!this.userInput) return;

            // Add the user's message to the messages array
            this.messages.push({ id: Date.now(), role: 'user', content: this.userInput });

            try {
                // Make the fetch request to the localized AJAX URL
                const response = await fetch(window.pluginData.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'handle_ai_request',  // Action name for WordPress AJAX
                        message: this.userInput
                    })
                });

                const data = await response.json();

                if (data.error) {
                    console.error('Error from AI server:', data.error);
                    return;
                }

                // Extract the AI response text from the Gemini API response
                const aiResponse = data.candidates[0]?.content.parts[0]?.text || 'No response from AI';

                // Add the AI's response to the messages array
                this.messages.push({ id: Date.now() + 1, role: 'ai', content: aiResponse });

                // Clear the user input
                this.userInput = '';

                // Auto-scroll to the bottom of the chat
                this.scrollToBottom();
            } catch (error) {
                console.error('Error communicating with AI server:', error);
            }
        },

        // Format the AI response content as HTML for better readability
        formatAIResponse(content) {
            let formattedContent = content;

            // Replace headings (##) with <h2> tags
            formattedContent = formattedContent.replace(/## (.+)/g, '<h2>$1</h2>');

            // Replace bold text (**) with <strong> tags
            formattedContent = formattedContent.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

            // Replace bullet points (*) with <li> tags, wrapped in <ul>
            formattedContent = formattedContent.replace(/\n\* (.+)/g, '<ul><li>$1</li></ul>');

            // Replace line breaks (\n) with <br> tags
            formattedContent = formattedContent.replace(/\n/g, '<br>');

            return formattedContent;
        },

        // Auto-scroll to the bottom of the chat box
        scrollToBottom() {
            this.$nextTick(() => {
                const chatBox = this.$refs.chatBox;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }
    },
    updated() {
        this.scrollToBottom(); // Ensure auto-scroll on every update
    }
};
</script>

<style scoped>
.chat-container {
    max-width: 800px; /* Increase the width for better layout */
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    font-family: 'Arial', sans-serif;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.chat-box {
    max-height: 400px; /* Increase the height for better visibility */
    overflow-y: auto;
    margin-bottom: 20px;
    padding: 10px;
    background-color: #fff;
    border-radius: 8px;
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.message {
    margin-bottom: 10px;
}

.message-content {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 5px;
    font-size: 14px;
    line-height: 1.5;
}

.message-content.user {
    background-color: #d1e7dd;
    text-align: left;
    border-left: 4px solid #007bff;
}

.message-content.ai {
    background-color: #e2e3e5;
    text-align: left;
    border-left: 4px solid #6c757d;
}

.message-content h2 {
    font-size: 16px;
    margin-top: 10px;
    margin-bottom: 5px;
}

.message-content ul {
    padding-left: 20px;
    margin-top: 5px;
    margin-bottom: 5px;
}

.message-content li {
    margin-bottom: 5px;
}

.chat-form {
    display: flex;
    margin-top: 10px;
}

.chat-form input {
    flex: 1;
    padding: 12px; /* Increased padding for better UX */
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.chat-form button {
    padding: 12px 20px; /* Increased padding for larger click area */
    margin-left: 8px;
    border: none;
    background-color: #007bff;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.chat-form button:hover {
    background-color: #0056b3;
}
</style>
