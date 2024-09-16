// Import your app and other dependencies
import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import router from './router';

// // Import Prism.js and its CSS theme
// import Prism from 'prismjs';
// import 'prismjs/themes/prism-tomorrow.css'; // Choose any Prism.js theme you like
// // Import the PHP component for Prism.js
// // Import PHP language support
// // Import the autoloader for dynamic language loading
// import 'prismjs/plugins/autoloader/prism-autoloader.min.js';
// // import 'prismjs/components/prism-php.min.js';
// // Configure Prism autoloader
// Prism.plugins.autoloader.languages_path = 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/'; // CDN path to Prism components
//
// // Import additional Prism plugins if needed
// import 'prismjs/plugins/line-numbers/prism-line-numbers.min.js';
// import 'prismjs/plugins/toolbar/prism-toolbar.min.js';
// import 'prismjs/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js';
//
// // Import CSS for plugins
// import 'prismjs/plugins/line-numbers/prism-line-numbers.css';
// import 'prismjs/plugins/toolbar/prism-toolbar.css';
//
// // Make Prism globally available
// window.Prism = Prism; // Ensure Prism is available globally



// highlight.js

// Import Highlight.js and its CSS theme
import hljs from 'highlight.js';
import 'highlight.js/styles/atom-one-dark.css'; // Choose any Highlight.js theme you like

// Create and mount the Vue app
const app = createApp(App);

app.directive('highlight', {
    mounted(el) {
        const blocks = el.querySelectorAll('pre code');
        blocks.forEach((block) => {
            hljs.highlightElement(block);
        });
    },
    updated(el) {
        const blocks = el.querySelectorAll('pre code');
        blocks.forEach((block) => {
            hljs.highlightElement(block);
        });
    },
});



app.use(createPinia());
app.use(router);

app.mount('#app');
