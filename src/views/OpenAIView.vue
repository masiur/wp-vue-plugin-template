<template>
    <div class="chat-container">
        <h1>OpenAI Chat Interface</h1>

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
import hljs from 'highlight.js'

export default {
    name: 'OpenAIView',
    data() {
        return {
            userInput: '', // User input from the form
            messages: JSON.parse(localStorage.getItem('openai_chat')) || [], // Load messages from localStorage or initialize an empty array
        };
    },
    methods: {
        async sendMessage() {
            if (!this.userInput) return;

            // Add the user's message to the messages array
            this.messages.push({ id: Date.now(), role: 'user', content: this.userInput });

            // Save messages to localStorage
            this.saveMessagesToLocalStorage();


            try {
                // Make the fetch request to the localized AJAX URL
                const response = await fetch(window.pluginData.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'handle_openai',  // Action name for WordPress AJAX
                        message: this.userInput
                    })
                });

                // Clear the user input
                this.userInput = '';

                const data = await response.json();

                if (data.error) {
                    console.error('Error from AI server:', data.error);
                    return;
                }

                // Extract the AI response text from the Open API response
                const aiResponse = data.choices[0]?.message?.content || 'No response from AI';

                // Add the AI's response to the messages array
                this.messages.push({ id: Date.now() + 1, role: 'ai', content: aiResponse });

                // Save messages to localStorage
                this.saveMessagesToLocalStorage();


                // Auto-scroll to the bottom of the chat
                this.scrollToBottom();
            } catch (error) {
                console.error('Error communicating with AI server:', error);
            }
        },

        // Save messages to localStorage
        saveMessagesToLocalStorage() {
            localStorage.setItem('openai_chat', JSON.stringify(this.messages));
        },

        // Format the AI response content as HTML for better readability
        formatAIResponse(content) {
            let formattedContent = content;

            // Dynamically replace code blocks with <pre><code> tags and retain language identifiers
            formattedContent = formattedContent.replace(/```(\w+)([\s\S]*?)```/g, '<pre><span>$1</span><code class="language-$1">$2</code></pre>');

            // Replace other elements as needed
            formattedContent = formattedContent.replace(/## (.+)/g, '<h2>$1</h2>');
            formattedContent = formattedContent.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            formattedContent = formattedContent.replace(/\n\* (.+)/g, '<ul><li>$1</li></ul>');
            // formattedContent = formattedContent.replace(/\n/g, '<br>');

            return formattedContent;
        },

        // Apply syntax highlighting to all code blocks
        highlightCodeBlocks() {
            const blocks = document.querySelectorAll('pre code');
            blocks.forEach((block) => {
                hljs.highlightElement(block);
            });
        },

        // Auto-scroll to the bottom of the chat box
        scrollToBottom() {
            this.$nextTick(() => {
                const chatBox = this.$refs.chatBox;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }
    },
    mounted() {
        // Auto-scroll to bottom when the component is mounted
        this.scrollToBottom();
        this.highlightCodeBlocks(); // Highlight code blocks on initial load
    },
    updated() {
        // Ensure auto-scroll on every update
        this.scrollToBottom();
        this.highlightCodeBlocks(); // Highlight code blocks after each update
    },
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
    max-height: 500px; /* Increase the height for better visibility */
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

.message-content pre {
    background-color: #2d2d2d;
    color: #f8f8f2;
    padding: 15px;
    border-radius: 6px;
    overflow-x: auto;
    font-family: 'Fira Code', 'Courier New', Courier, monospace;
    font-size: 13px;
    margin-top: 10px;
    margin-bottom: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border: 1px solid #444;
}

.message-content code {
    background-color: #2d2d2d;
    color: #f8f8f2;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: 'Fira Code', 'Courier New', Courier, monospace;
    font-size: 13px;
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

<style>


/* Enhanced Code Block Styling */
.code-block {
    display: flex;
    background-color: #2d2d2d; /* Dark background for better contrast */
    color: #f8f8f2; /* Light text color for readability */
    padding: 5px; /* Increased padding for more space around code */
    border-radius: 6px; /* Rounded corners for a modern look */
    overflow-x: auto; /* Enable horizontal scrolling for long lines */
    font-family: 'Fira Code', 'Courier New', Courier, monospace; /* Monospaced font for code */
    font-size: 13px; /* Slightly smaller font size for code */
    border: 1px solid #444; /* Subtle border to define the code area */
    line-height: 1.5; /* Improved line height for better readability */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
    white-space: pre-wrap; /* Preserve whitespace and wrap text properly */
    margin-top: 10px;
    margin-bottom: 10px;
    max-width: 100%; /* Prevent overflow */
}


</style>