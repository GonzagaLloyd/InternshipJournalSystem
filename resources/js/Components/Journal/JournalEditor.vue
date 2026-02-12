<script setup>
import { onBeforeUnmount, ref } from 'vue';
import axios from 'axios';
import AssetPalette from './AssetPalette.vue';

const props = defineProps({
    form: Object,
    formattedDate: String
});

const emit = defineEmits(['submit']);

const isFocused = ref(false);
const isRefining = ref(false);
const aiError = ref(null);
const previewUrls = ref({});

const getPreviewUrl = (file, key) => {
    if (!file || !(file instanceof File || file instanceof Blob)) {
        return '';
    }
    
    // Revoke old URL if it exists to prevent memory leaks
    if (previewUrls.value[key]) {
        window.URL.revokeObjectURL(previewUrls.value[key]);
    }
    
    const url = window.URL.createObjectURL(file);
    previewUrls.value[key] = url;
    return url;
};

const refineContent = async () => {
    if (!props.form.content || props.form.content.length < 5 || isRefining.value || props.form.processing) return;
    
    isRefining.value = true;
    aiError.value = null;
    
    try {
        const response = await axios.post(route('journal.refine'), {
            content: props.form.content
        });
        
        if (response.data.refinedText) {
            props.form.content = response.data.refinedText;
        }
    } catch (error) {
        aiError.value = error.response?.data?.error || 'The Muse is silent...';
        setTimeout(() => aiError.value = null, 5000);
    } finally {
        isRefining.value = false;
    }
};

const handleSealDecree = () => {
    if (!props.form.content || props.form.processing || isRefining.value) return;
    emit('submit');
};

onBeforeUnmount(() => {
    Object.values(previewUrls.value).forEach(url => window.URL.revokeObjectURL(url));
});
</script>

<template>
    <div 
        class="flex-1 flex flex-col relative overflow-hidden group/editor min-h-0"
    >
        <!-- Background (Textured but Clean) -->
        <div 
            class="absolute inset-0 bg-[#161616] rounded-[2rem] border border-white/[0.04] shadow-2xl"
        ></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')] opacity-[0.03] pointer-events-none rounded-[2rem]"></div>

        <!-- Subtle Border Frame -->
        <div class="absolute inset-0 border border-white/[0.05] rounded-[2rem] pointer-events-none z-30"></div>
        
        <!-- Header -->
        <div class="px-4 md:px-10 lg:px-12 py-5 md:py-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-5 md:gap-6 relative z-20 border-b border-white/[0.04] shrink-0">
            <div class="flex-shrink-0 min-w-0 max-w-full">
                <p class="text-[8px] md:text-[9px] uppercase tracking-[0.4em] text-[#A68B6A]/60 font-bold mb-0.5 md:mb-1">Codex Entry</p>
                <h2 class="text-lg md:text-2xl font-bold text-[#E3D5C1] font-cinzel italic truncate max-w-[200px] sm:max-w-[300px] md:max-w-none">{{ formattedDate }}</h2>
            </div>
            
            <div class="flex flex-row flex-wrap items-center gap-3 md:gap-4 w-full md:w-auto">
                <!-- AI Refinement Button -->
                <button 
                    class="group/muse relative h-10 md:h-11 w-full md:w-44 bg-white/[0.04] text-[#E3D5C1] rounded-xl border border-white/[0.08] transition-all hover:bg-white/[0.1] active:scale-95 disabled:opacity-20 flex-1 md:flex-none flex items-center justify-center"
                    @click="refineContent"
                    :disabled="isRefining || form.processing || !form.content"
                >
                    <div class="flex items-center gap-2">
                        <svg 
                            :class="{'animate-spin': isRefining}"
                            class="w-3.5 h-3.5 md:w-4 md:h-4 text-[#A68B6A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-[10px] md:text-[11px] uppercase tracking-[0.15em] font-black whitespace-nowrap">
                            {{ isRefining ? 'Musing...' : 'Consult Muse' }}
                        </span>
                    </div>
                </button>

                <!-- Save Button -->
                <button 
                    class="h-10 md:h-11 w-full md:w-44 bg-[#A68B6A] text-[#1B1B1B] rounded-xl transition-all hover:bg-[#C9B79C] hover:scale-[1.02] active:scale-95 disabled:opacity-20 flex-1 md:flex-none flex items-center justify-center font-black text-[10px] md:text-[11px] uppercase tracking-[0.15em] whitespace-nowrap shadow-xl"
                    @click="handleSealDecree"
                    :disabled="form.processing || isRefining || !form.content"
                >
                    {{ form.processing ? 'Sealing...' : 'Seal Decree' }}
                </button>
            </div>
        </div>

        <!-- Centered Writing Area -->
        <div class="flex-1 overflow-y-auto relative z-10 scrollbar-hide">
            <div class="max-w-3xl mx-auto w-full px-6 md:px-12 pt-10 pb-20 relative">
                <!-- Title -->
                <div class="mb-10 group/title">
                    <input 
                        v-model="form.title"
                        placeholder="Chapter Title..."
                        class="w-full bg-transparent border-none focus:ring-0 p-0 text-xl md:text-3xl font-bold font-cinzel text-[#E3D5C1] placeholder:text-[#A68B6A]/30 placeholder:italic"
                    />
                    <div class="h-[1px] w-12 bg-[#A68B6A]/20 mt-4 group-hover/title:w-20 transition-all duration-700"></div>
                </div>

                <!-- Content Area -->
                <textarea 
                    v-model="form.content"
                    @focus="isFocused = true"
                    @blur="isFocused = false"
                    placeholder="Inscribe the day's lore..."
                    class="w-full bg-transparent border-none focus:ring-0 p-0 text-base md:text-lg leading-[2.2] text-[#E3D5C1]/70 placeholder:text-[#E3D5C1]/10 resize-none font-serif min-h-[500px]"
                    :class="{'text-[#E3D5C1]': isFocused}"
                ></textarea>

                <!-- Attachments (Subtle Footer Style) -->
                <div v-if="form.image || form.video || form.audio || form.file" class="mt-16 pt-10 border-t border-white/[0.05] space-y-6">
                    <p class="text-[9px] uppercase tracking-[0.4em] text-[#A68B6A]/50 font-bold">Chronicle Assets</p>
                    <div class="flex flex-wrap gap-5">
                        <div 
                            v-for="type in ['image', 'video', 'audio', 'file'].filter(t => form[t])" 
                            :key="type"
                            class="group/asset-item relative h-20 w-32 bg-white/[0.03] border border-white/[0.05] rounded-xl flex flex-col items-center justify-center transition-all hover:border-[#A68B6A]/30 overflow-hidden"
                        >
                            <img v-if="type === 'image'" :src="getPreviewUrl(form.image, 'image')" class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover/asset-item:opacity-60 transition-opacity" />
                            <div class="relative z-10 flex flex-col items-center">
                                <svg class="w-4 h-4 text-[#A68B6A] mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <span class="text-[8px] text-[#E3D5C1] uppercase tracking-widest truncate w-24 text-center font-bold">{{ form[type].name || type }}</span>
                            </div>
                            <button @click="form[type] = null" class="absolute inset-0 bg-black/90 opacity-0 group-hover/asset-item:opacity-100 flex items-center justify-center transition-opacity">
                                <span class="text-[8px] text-white uppercase font-black tracking-widest">Discard Entry</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Minimal Asset Peeking Palette (Right Side) -->
        <div class="absolute right-0 top-1/2 -translate-y-1/2 z-40 group-hover/editor:translate-x-[-10px] transition-all duration-700">
            <AssetPalette :form="form" />
        </div>
    </div>
</template>

<style scoped>
@keyframes shimmer {
    100% { transform: translateX(100%); }
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.cubic-bezier-standard {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

