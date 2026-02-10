<script setup>
import { onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    form: Object,
    formattedDate: String
});

const emit = defineEmits(['submit']);

const isFocused = ref(false);
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

onBeforeUnmount(() => {
    Object.values(previewUrls.value).forEach(url => window.URL.revokeObjectURL(url));
});
</script>

<template>
    <div 
        :class="isFocused ? 'bg-[#1a1817] shadow-2xl' : 'bg-transparent border-white/[0.05]'"
        class="flex-1 flex flex-col border rounded-[2.5rem] relative overflow-hidden transition-all duration-700 ease-out group/editor"
    >
        <!-- Minimal Depth -->
        <div 
            :class="isFocused ? 'opacity-100' : 'opacity-0'"
            class="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent pointer-events-none transition-opacity duration-700"
        ></div>
        
        <!-- Header -->
        <div class="px-10 lg:px-14 pt-10 pb-6 flex flex-col sm:flex-row justify-between items-center gap-6 relative z-20">
            <div class="text-center sm:text-left">
                <p class="text-[9px] uppercase tracking-[0.4em] text-[#8b2635] font-black mb-1 opacity-60">Entry</p>
                <h2 class="text-3xl font-bold text-[#f4e4bc] font-cinzel tracking-tight italic">{{ formattedDate }}</h2>
            </div>
            
            <button 
                class="group relative px-10 py-3.5 bg-[#8b2635] text-[#f4e4bc] rounded-xl overflow-hidden transition-all hover:translate-y-[-2px] active:translate-y-0 shrink-0 shadow-lg"
                @click="$emit('submit')"
            >
                <span class="relative z-10 text-[10px] font-black uppercase tracking-[0.4em]">Seal Entry</span>
            </button>
        </div>

        <!-- Scrollable Area -->
        <div class="flex-1 overflow-y-auto relative z-10 scrollbar-hide px-10 lg:px-14 flex flex-col pt-4">
            <!-- Title -->
            <div class="mb-10">
                <input 
                    v-model="form.title"
                    @focus="isFocused = true"
                    @blur="isFocused = false"
                    placeholder="Chapter Title..."
                    class="w-full bg-transparent border-none focus:ring-0 p-0 text-2xl lg:text-3xl font-bold font-cinzel text-[#f4e4bc] placeholder:text-[#3d352e] placeholder:italic transition-all"
                />
            </div>

            <!-- Content Area -->
            <textarea 
                v-model="form.content"
                @focus="isFocused = true"
                @blur="isFocused = false"
                placeholder="Inscribe the day's lore..."
                class="flex-1 w-full bg-transparent border-none focus:ring-0 p-0 text-lg lg:text-xl font-medium leading-[2] text-[#d9c5a3]/90 placeholder:text-[#3d352e] resize-none min-h-[300px] font-serif transition-all"
            ></textarea>

            <!-- Attachments Preview -->
            <div v-if="form.image || form.video || form.audio || form.file" class="py-12 space-y-8">
                <div class="flex items-center gap-4 opacity-30">
                    <span class="text-[9px] uppercase tracking-[0.4em] text-[#8c7e6a] font-black font-sans">Attachments</span>
                    <div class="h-[1px] w-full bg-white/[0.05]"></div>
                </div>

                <div class="flex flex-wrap gap-5">
                    <!-- Image -->
                    <div v-if="form.image" class="relative group h-24 w-24 bg-black/60 rounded-2xl overflow-hidden shadow-xl border border-white/[0.05]">
                        <img :src="getPreviewUrl(form.image, 'image')" class="w-full h-full object-cover" />
                        <button @click="form.image = null" class="absolute inset-0 bg-black/80 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                            <span class="text-[8px] text-white uppercase font-black tracking-widest">Discard</span>
                        </button>
                    </div>

                    <!-- Other Assets -->
                    <div 
                        v-for="type in ['video', 'audio', 'file'].filter(t => form[t])" 
                        :key="type"
                        class="relative group h-24 w-36 bg-white/[0.03] border border-white/[0.05] rounded-2xl flex flex-col items-center justify-center p-3 transition-colors hover:border-[#8b2635]/30"
                    >
                        <div class="h-8 w-8 rounded-full bg-black/20 flex items-center justify-center mb-2">
                            <svg class="w-3.5 h-3.5 text-[#8c7e6a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </div>
                        <span class="text-[9px] text-[#8c7e6a] uppercase tracking-widest truncate w-full text-center px-2">{{ form[type].name || `${type} attached` }}</span>
                        <button @click="form[type] = null" class="absolute inset-0 bg-black/95 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                            <span class="text-[8px] text-white uppercase font-black tracking-widest">Discard</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Asset Palette -->
            <div 
                :class="isFocused ? 'border-white/10 bg-black/20' : 'border-transparent'"
                class="mt-auto flex flex-wrap items-center gap-8 py-8 border-t transition-all duration-700 ease-in-out px-4"
            >
                <!-- Inputs -->
                <div class="flex items-center gap-6">
                    <div class="flex gap-2">
                        <label v-for="type in ['image', 'video', 'file', 'audio']" :key="type" class="cursor-pointer p-4 bg-white/[0.03] border border-white/[0.05] rounded-xl text-[#8c7e6a] hover:text-[#f4e4bc] hover:border-[#8b2635]/40 hover:bg-[#8b2635]/10 transition-all duration-300">
                            <input type="file" @input="form[type] = $event.target.files[0]" class="hidden" :accept="type === 'image' ? 'image/*' : type === 'video' ? 'video/*' : type === 'audio' ? 'audio/*' : '*'" />
                            <svg v-if="type === 'image'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <svg v-else-if="type === 'video'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <svg v-else-if="type === 'audio'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
