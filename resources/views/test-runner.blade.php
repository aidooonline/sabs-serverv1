<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SABS v3 - Unit Test Runner</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .ansi-green { color: #22c55e; }
        .ansi-red { color: #ef4444; }
        .ansi-yellow { color: #eab308; }
        pre { font-family: 'Fira Code', 'Consolas', monospace; }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 font-sans h-screen flex flex-col overflow-hidden" 
      x-data="testRunner()" 
      @run-test-event.window="runTest($event.detail.path)">

    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 p-4 flex justify-between items-center shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
            <h1 class="text-xl font-bold tracking-wide text-white">SABS v3 <span class="text-gray-400 font-normal">Test Runner</span></h1>
        </div>
        <div class="text-sm text-gray-400">
            Environment: <span class="text-yellow-500 font-mono">{{ app()->environment() }}</span>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- Sidebar: File Explorer -->
        <aside class="w-80 bg-gray-800/50 border-r border-gray-700 flex flex-col">
            <div class="p-4 border-b border-gray-700">
                <button @click="runTest('tests')" 
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded shadow transition-colors flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    Run All Tests
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-1">
                <!-- Recursive Component Logic handled via JS helper below -->
                <div id="file-tree" class="text-sm space-y-2">
                    <!-- Rendered by Alpine/JS -->
                </div>
            </div>
        </aside>

        <!-- Main Output Area -->
        <main class="flex-1 flex flex-col bg-black relative">
            
            <!-- Toolbar -->
            <div class="bg-gray-800/80 p-2 flex items-center justify-between text-xs border-b border-gray-700">
                <span class="font-mono text-gray-400" x-text="currentPath ? 'Target: ' + currentPath : 'Ready'"></span>
                <button @click="clearOutput" class="text-gray-400 hover:text-white hover:underline">Clear Console</button>
            </div>

            <!-- Terminal Output -->
            <div class="flex-1 overflow-auto p-6 font-mono text-sm leading-relaxed" id="terminal-output">
                <template x-if="!output && !loading">
                    <div class="text-gray-500 text-center mt-20">
                        <p class="mb-2">Select a file or folder to run tests.</p>
                        <p class="text-xs opacity-50">Results will appear here.</p>
                    </div>
                </template>

                <template x-if="loading">
                    <div class="flex items-center gap-2 text-blue-400 animate-pulse">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Executing tests...</span>
                    </div>
                </template>

                <div x-html="formattedOutput" class="whitespace-pre-wrap break-words"></div>
            </div>
        </main>
    </div>

    <script>
        // Pass PHP data to JS
        const initialStructure = @json($structure);

        function testRunner() {
            return {
                structure: initialStructure,
                output: '',
                loading: false,
                currentPath: '',

                init() {
                    const treeContainer = document.getElementById('file-tree');
                    treeContainer.innerHTML = this.renderTree(this.structure);
                },

                renderTree(items, depth = 0) {
                    let html = '';
                    items.forEach(item => {
                        const padding = depth * 16;
                        const isFolder = item.type === 'folder';
                        const icon = isFolder 
                            ? `<svg class="w-4 h-4 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>`
                            : `<svg class="w-4 h-4 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`;
                        
                        // We use a global function call to trigger Alpine method
                        html += `
                            <div class="cursor-pointer group">
                                <div onclick="window.triggerRunTest('${item.path}')" 
                                     class="flex items-center hover:bg-gray-700 py-1 px-2 rounded select-none transition-colors text-gray-300 hover:text-white"
                                     style="padding-left: ${padding}px">
                                    ${icon}
                                    <span class="truncate">${item.name}</span>
                                </div>
                                ${isFolder && item.children ? this.renderTree(item.children, depth + 1) : ''}
                            </div>
                        `;
                    });
                    return html;
                },

                runTest(path) {
                    this.loading = true;
                    this.output = '';
                    this.currentPath = path;

                    // Use Laravel route() to generate the correct URL (handling index.php or subfolders)
                    const url = "{{ route('dev.run_test') }}?path=" + encodeURIComponent(path);

                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.loading = false;
                        this.output = data.output;
                    })
                    .catch(err => {
                        this.loading = false;
                        this.output = 'Error executing test: ' + err;
                    });
                },

                clearOutput() {
                    this.output = '';
                    this.currentPath = '';
                },

                get formattedOutput() {
                    if (!this.output) return '';
                    // Basic ANSI color parsing
                    let text = this.output
                        .replace(/\[32m/g, '<span class="text-green-500">')
                        .replace(/\[31m/g, '<span class="text-red-500">')
                        .replace(/\[33m/g, '<span class="text-yellow-500">')
                        .replace(/\[39m/g, '</span>')
                        .replace(/\[0m/g, '</span>');
                    return text;
                }
            }
        }

        // Bridge for inline HTML onclicks to reach Alpine component
        window.triggerRunTest = (path) => {
            window.dispatchEvent(new CustomEvent('run-test-event', { detail: { path: path } }));
        };
    </script>
</body>
</html>