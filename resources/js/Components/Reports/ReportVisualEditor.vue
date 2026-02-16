<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    blocks: Array,
    isVisualMode: Boolean,
    rawMarkdown: String,
    standalone: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:rawMarkdown', 'update:blocks']);

const autoResize = (event) => {
    const el = event.target;
    el.style.height = 'auto';
    el.style.height = (el.scrollHeight) + 'px';
};

const handleRawInput = (e) => {
    emit('update:rawMarkdown', e.target.value);
};

onMounted(() => {
    const textareas = document.querySelectorAll('.visual-editor-textarea');
    textareas.forEach(ta => {
        ta.style.height = 'auto';
        ta.style.height = ta.scrollHeight + 'px';
    });
});
</script>

<template>
    <div :class="['h-full flex flex-col', standalone ? 'gap-0' : 'gap-5']">
        <!-- Refined Info & Toggle Bar - Only if NOT standalone -->
        <div v-if="!standalone" class="flex items-center justify-between px-3 py-2 bg-white/[0.02] border-b border-white/[0.05] rounded-t-2xl shrink-0">
            <div class="flex items-center gap-3 text-[10px] text-[#A68B6A]/80 font-sans tracking-wide">
                <div class="p-1 rounded bg-[#A68B6A]/10">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span v-if="isVisualMode" class="font-medium">Visual Mode: Direct formatting active.</span>
                <span v-else class="font-medium">Raw MD Mode: Stylized symbols enabled.</span>
            </div>
            <slot name="toggle-button"></slot>
        </div>

        <!-- Scrollable Content Area -->
        <div v-if="isVisualMode" :class="['flex-1 overflow-y-auto custom-scrollbar space-y-2', standalone ? 'pt-10 pb-20 px-0' : 'pr-3']">
            
            <div v-if="standalone" class="flex justify-end mb-8">
                 <slot name="toggle-button"></slot>
            </div>

            <div v-for="(block, index) in blocks" :key="index" class="relative group/block max-w-4xl mx-auto">
                <!-- Header 1 -->
                <textarea 
                    v-if="block.type === 'h1'"
                    v-model="block.content"
                    @input="autoResize"
                    class="visual-editor-textarea w-full bg-transparent border-none focus:ring-0 p-0 text-3xl font-bold text-[#E3D5C1] font-cinzel resize-none overflow-hidden text-center mb-8 placeholder:text-[#A68B6A]/20"
                    placeholder="CHRONICLE TITLE"
                ></textarea>
                
                <!-- Header 2 -->
                <div v-else-if="block.type === 'h2'" class="flex flex-col gap-4 mt-12 mb-6 text-center group/h2 p-4 rounded-xl transition-all duration-300 focus-within:bg-white/[0.02] focus-within:shadow-[0_0_30px_rgba(166,139,106,0.03)]">
                    <textarea 
                        v-model="block.content"
                        @input="autoResize"
                        class="visual-editor-textarea w-full bg-transparent border-none focus:ring-0 p-0 text-2xl font-bold text-[#A68B6A] font-cinzel resize-none overflow-hidden text-center italic placeholder:text-[#A68B6A]/10"
                        placeholder="SECTION HEADING"
                    ></textarea>
                    <div class="h-[1px] w-12 bg-[#A68B6A]/30 mx-auto transition-all group-focus-within/h2:w-24 group-focus-within/h2:bg-[#A68B6A]"></div>
                </div>

                <!-- Header 3 -->
                <textarea 
                    v-else-if="block.type === 'h3'"
                    v-model="block.content"
                    @input="autoResize"
                    class="visual-editor-textarea w-full bg-transparent border-none focus:ring-0 p-0 text-xl font-bold text-[#E3D5C1] resize-none overflow-hidden mt-8 mb-4 text-center"
                    placeholder="Subhead..."
                ></textarea>

                <!-- List Item -->
                <div v-else-if="block.type === 'li'" class="group/li px-4 py-2 rounded-xl transition-all duration-300 focus-within:bg-white/[0.02] focus-within:shadow-[0_0_20px_rgba(166,139,106,0.02)]">
                    <div class="flex gap-4">
                        <span class="text-[#A68B6A] mt-2 transition-transform group-focus-within/li:scale-125">•</span>
                        <textarea 
                            v-model="block.content"
                            @input="autoResize"
                            class="visual-editor-textarea w-full bg-transparent border-none focus:ring-0 p-0 text-lg py-1.5 text-[#E3D5C1]/70 resize-none overflow-hidden font-serif italic placeholder:text-[#A68B6A]/20"
                            placeholder="Add a technical accomplishment or challenge..."
                        ></textarea>
                    </div>
                </div>

                <div 
                    v-else-if="block.type === 'p'" 
                    class="relative group/p rounded-xl transition-all duration-300 focus-within:bg-white/[0.02] focus-within:shadow-[0_0_30px_rgba(166,139,106,0.03)]"
                >
                    <textarea 
                        v-model="block.content"
                        @input="autoResize"
                        class="visual-editor-textarea w-full bg-transparent border-none focus:ring-0 p-4 text-lg leading-relaxed text-[#E3D5C1]/80 resize-none overflow-hidden font-serif text-justify placeholder:text-[#A68B6A]/20"
                        placeholder="Begin documenting the weekly progress here..."
                    ></textarea>
                </div>

                <div v-else-if="block.type === 'br'" class="h-6"></div>
            </div>
        </div>

        <!-- Raw Markdown Editor -->
        <div v-else :class="['flex-1 flex flex-col', standalone ? 'pt-10 pb-20' : '']">
            <div v-if="standalone" class="flex justify-end mb-4 px-4">
                 <slot name="toggle-button"></slot>
            </div>
            <textarea 
                :value="rawMarkdown"
                @input="handleRawInput"
                class="flex-1 w-full bg-[#1A1A1A]/50 border border-white/10 rounded-xl p-8 text-[#E3D5C1] font-mono text-sm leading-relaxed focus:border-[#A68B6A]/50 focus:ring-1 focus:ring-[#A68B6A]/50 transition-all resize-none custom-scrollbar placeholder-white/20"
                placeholder="Compose the chronicle in raw form..."
            ></textarea>
        </div>
    </div>
</template>

<style scoped>
.visual-editor-textarea::placeholder {
    opacity: 1;
    transition: opacity 0.3s ease;
}

.visual-editor-textarea:focus::placeholder {
    opacity: 0.3;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.02);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(166, 139, 106, 0.3);
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(166, 139, 106, 0.5);
}

@keyframes focus-pulse {
    0% { border-color: rgba(166, 139, 106, 0.1); }
    50% { border-color: rgba(166, 139, 106, 0.4); }
    100% { border-color: rgba(166, 139, 106, 0.1); }
}
</style>
