<script setup>
import { ref } from 'vue';

const props = defineProps({
    entry: Object
});

const imageError = ref(false);
const videoError = ref(false);
const audioError = ref(false);

// Default Paths
const defaultImage = '/images/defaults/default_image.jfif';
const defaultVideo = '/images/defaults/default_video.jfif';
const defaultAudioThumbnail = '/images/defaults/default_audio.jfif';

const handleImageError = (e) => {
    if (e.target.src !== location.origin + defaultImage) {
        e.target.src = defaultImage;
    } else {
        imageError.value = true;
    }
};

const handleVideoError = (e) => {
    videoError.value = true; 
};

const handleAudioError = (e) => {
    audioError.value = true;
};
</script>

<template>
    <!-- Media Section: "The Archives" -->
    <div v-if="entry.image || entry.video || entry.audio" class="mb-16 pt-16 border-t border-white/5 relative font-serif">
        <!-- Section Title -->
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#1B1B1B] px-6 text-[#8C6A4A] text-[10px] sm:text-xs font-cinzel tracking-[0.4em] uppercase flex items-center gap-2 whitespace-nowrap">
            <span class="w-1 h-1 rounded-full bg-[#8C6A4A]"></span>
            Attached Evidence
            <span class="w-1 h-1 rounded-full bg-[#8C6A4A]"></span>
        </div>

        <div class="grid grid-cols-1 gap-12">
            
            <!-- Image Card -->
            <div v-if="entry.image" class="bg-[#2D2D2D]/40 border border-white/5 p-2 rounded-sm shadow-2xl group hover:border-[#8C6A4A]/40 transition-all duration-500">
                <!-- Card Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-white/5 mb-2">
                    <div class="flex items-center gap-2 text-[#8C6A4A]/60 group-hover:text-[#C9B79C] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Visual Plate</span>
                    </div>
                    <div class="w-12 h-[1px] bg-white/5 group-hover:bg-[#8C6A4A]/20 transition-colors"></div>
                </div>
                
                <!-- Content -->
                <div class="relative overflow-hidden bg-black/40">
                    <div v-if="!imageError" class="relative">
                        <div class="absolute inset-0 bg-[#1B1B1B]/10 group-hover:bg-transparent transition-all duration-700 pointer-events-none z-10"></div>
                        <img :src="'/storage/' + entry.image" @error="handleImageError" class="w-full h-auto object-cover opacity-90 group-hover:opacity-100 transition-all duration-700 select-none" alt="Entry visual">
                        <!-- Corner Accents -->
                        <div class="absolute top-2 left-2 w-2 h-2 border-t border-l border-[#C9B79C]/30"></div>
                        <div class="absolute bottom-2 right-2 w-2 h-2 border-b border-r border-[#C9B79C]/30"></div>
                    </div>
                    <div v-else class="w-full h-64 flex flex-col items-center justify-center p-8 text-[#8C6A4A]/60 relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/black-loose-fabric.png')]"></div>
                            <div class="relative z-10 flex flex-col items-center">
                            <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            <span class="text-[9px] font-cinzel tracking-[0.3em] uppercase opacity-60">Image Corrupted</span>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Video Card -->
                <div v-if="entry.video" class="bg-[#2D2D2D]/40 border border-white/5 p-2 rounded-sm shadow-2xl group hover:border-[#8C6A4A]/40 transition-all duration-500">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/5 mb-2">
                    <div class="flex items-center gap-2 text-[#8C6A4A]/60 group-hover:text-[#C9B79C] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Moving Record</span>
                    </div>
                    <div class="w-12 h-[1px] bg-white/5 group-hover:bg-[#8C6A4A]/20 transition-colors"></div>
                </div>
                
                <div class="relative overflow-hidden bg-black/40">
                    <video v-if="!videoError" controls class="w-full opacity-90 hover:opacity-100 transition-opacity" @error="handleVideoError" :poster="defaultVideo">
                        <source :src="'/storage/' + entry.video">
                    </video>
                    <div v-else class="w-full h-48 flex flex-col items-center justify-center p-8 text-[#8C6A4A]/60 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/black-loose-fabric.png')]"></div>
                        <div class="relative z-10 flex flex-col items-center">
                            <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            <span class="text-[9px] font-cinzel tracking-[0.3em] uppercase opacity-60">Signal Lost</span>
                        </div>
                    </div>
                </div>
                </div>

            <!-- Audio Card -->
            <div v-if="entry.audio" class="bg-[#2D2D2D]/40 border border-white/5 p-6 rounded-sm shadow-xl group hover:border-[#8C6A4A]/40 transition-all duration-500 relative overflow-hidden">
                <!-- Background waveform hint -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                    <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/sound-waves.png')]"></div>
                </div>

                <div class="relative z-10 flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-white/5 pb-3">
                        <div class="flex items-center gap-2 text-[#8C6A4A]/60 group-hover:text-[#C9B79C] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                            <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Voice Echo</span>
                        </div>
                        <div class="flex gap-1">
                            <span class="w-1 h-1 rounded-full bg-white/5 group-hover:bg-[#8C6A4A] transition-colors"></span>
                            <span class="w-1 h-1 rounded-full bg-white/5 group-hover:bg-[#8C6A4A] transition-colors delay-75"></span>
                            <span class="w-1 h-1 rounded-full bg-white/5 group-hover:bg-[#8C6A4A] transition-colors delay-150"></span>
                        </div>
                    </div>

                    <audio v-if="!audioError" controls class="w-full h-10 custom-audio-simple opacity-80 hover:opacity-100 transition-opacity" @error="handleAudioError">
                        <source :src="'/storage/' + entry.audio">
                    </audio>
                    <div v-else class="flex items-center justify-center py-4 bg-black/20 rounded-sm border border-white/5">
                        <span class="text-[9px] font-cinzel tracking-[0.3em] uppercase text-[#8C6A4A]/50">Audio Fragment Damaged</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
audio.custom-audio-simple {
   height: 32px;
   filter: sepia(0.8) hue-rotate(350deg) saturate(0.8);
}
</style>
