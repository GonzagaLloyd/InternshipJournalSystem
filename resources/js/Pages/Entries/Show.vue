<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    entry: Object
});

const imageError = ref(false);
const videoError = ref(false);
const audioError = ref(false);

// Default Paths
const defaultImage = '/images/defaults/default_image.jfif';
const defaultVideo = '/images/defaults/default_video.jfif'; // In practice, might be an image thumbnail for video
const defaultAudioThumbnail = '/images/defaults/default_audio.jfif';

const handleImageError = (e) => {
    if (e.target.src !== location.origin + defaultImage) {
        e.target.src = defaultImage;
    } else {
        imageError.value = true;
    }
};

const handleVideoError = (e) => {
    // For video, we might replace the poster or just show error
    videoError.value = true; 
};

const handleAudioError = (e) => {
    audioError.value = true;
};

const formatDate = (dateString) => {
    return new Intl.DateTimeFormat('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    }).format(new Date(dateString));
};
</script>

<template>
    <Head :title="entry.title" />

    <div class="h-full bg-[#161412] text-[#d9c5a3] p-4 md:p-8 flex flex-col items-center relative overflow-hidden font-cormorant min-h-screen">
            
            <!-- Dust Specs Layer (Matched to Index) -->
            <div class="absolute inset-0 pointer-events-none z-20 overflow-hidden">
                <div class="dust-particles opacity-[0.1]"></div>
            </div>

            <!-- Background Texture (Matched to Index) -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>

            <!-- Back Navigation -->
            <div class="w-full max-w-4xl z-30 mb-8">
                 <Link :href="route('journal.index')" class="inline-flex items-center gap-2 text-[#8c7e6a] hover:text-[#f4e4bc] transition-colors duration-300 group">
                    <div class="w-8 h-8 rounded-full border border-[#3d352e] flex items-center justify-center group-hover:border-[#8b2635]/60 transition-all">
                        <svg class="w-3 h-3 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Return to Ledger</span>
                </Link>
            </div>

            <!-- Main Entry Container -->
            <article class="w-full max-w-4xl relative z-30 bg-[#1e1b19]/40 backdrop-blur-[2px] border border-[#3d352e]/30 p-8 md:p-12 lg:p-16 rounded-sm shadow-2xl animate-fade-in-up">
                
                <!-- Ornamental Border Corners -->
                <div class="absolute top-0 left-0 w-16 h-16 border-t border-l border-[#8b2635]/20 rounded-tl-sm"></div>
                <div class="absolute top-0 right-0 w-16 h-16 border-t border-r border-[#8b2635]/20 rounded-tr-sm"></div>
                <div class="absolute bottom-0 left-0 w-16 h-16 border-b border-l border-[#8b2635]/20 rounded-bl-sm"></div>
                <div class="absolute bottom-0 right-0 w-16 h-16 border-b border-r border-[#8b2635]/20 rounded-br-sm"></div>

                <!-- Header -->
                <header class="text-center mb-16 relative">
                    <div class="mb-6 flex flex-col items-center gap-3">
                         <div class="h-12 w-[1px] bg-gradient-to-b from-transparent via-[#8b2635]/40 to-transparent"></div>
                         <span class="text-[11px] font-cinzel tracking-[0.4em] text-[#8b2635] uppercase bg-[#8b2635]/5 px-4 py-1 rounded-full border border-[#8b2635]/10">
                            {{ formatDate(entry.entry_date) }}
                        </span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-cinzel font-bold text-[#f4e4bc] mb-8 leading-tight tracking-tight drop-shadow-sm">
                        {{ entry.title }}
                    </h1>
                     <div class="w-24 h-[1px] bg-gradient-to-r from-transparent via-[#f4e4bc]/30 to-transparent mx-auto"></div>
                </header>

                <!-- Media Section -->
                <!-- Content -->
                <div class="prose prose-xl prose-invert max-w-none text-[#d9c5a3] font-cormorant leading-relaxed tracking-wide text-justify selection:bg-[#8b2635]/20 selection:text-[#f4e4bc] mb-16">
                     <div class="first-letter:text-5xl first-letter:font-cinzel first-letter:text-[#f4e4bc] first-letter:mr-3 first-letter:float-left first-letter:leading-[0.8]">
                        <p class="whitespace-pre-wrap">{{ entry.content }}</p>
                    </div>
                </div>

                <!-- Media Section: "The Archives" -->
                <div v-if="entry.image || entry.video || entry.audio" class="mb-16 pt-16 border-t border-[#8b2635]/20 relative">
                    <!-- Section Title -->
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#161412] px-6 text-[#8b2635] text-xs font-cinzel tracking-[0.4em uppercase font-bold flex items-center gap-2">
                        <span class="w-1 h-1 rounded-full bg-[#8b2635]"></span>
                        Attached Evidence
                        <span class="w-1 h-1 rounded-full bg-[#8b2635]"></span>
                    </div>

                    <div class="grid grid-cols-1 gap-12">
                        
                        <!-- Image Card -->
                        <div v-if="entry.image" class="bg-[#1e1b19]/60 border border-[#3d352e] p-2 rounded-sm shadow-2xl group hover:border-[#8b2635]/40 transition-all duration-500">
                            <!-- Card Header -->
                            <div class="flex items-center justify-between px-4 py-3 border-b border-[#3d352e]/50 mb-2">
                                <div class="flex items-center gap-2 text-[#8c7e6a] group-hover:text-[#f4e4bc] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Visual Plate</span>
                                </div>
                                <div class="w-12 h-[1px] bg-[#3d352e] group-hover:bg-[#8b2635]/50 transition-colors"></div>
                            </div>
                            
                            <!-- Content -->
                            <div class="relative overflow-hidden bg-black/40">
                                <div v-if="!imageError" class="relative">
                                    <div class="absolute inset-0 bg-[#161412]/10 group-hover:bg-transparent transition-all duration-700 pointer-events-none z-10"></div>
                                    <img :src="'/storage/' + entry.image" @error="handleImageError" class="w-full h-auto object-cover opacity-90 group-hover:opacity-100 transition-all duration-700 select-none" alt="Entry visual">
                                    <!-- Corner Accents -->
                                    <div class="absolute top-2 left-2 w-2 h-2 border-t border-l border-[#f4e4bc]/30"></div>
                                    <div class="absolute bottom-2 right-2 w-2 h-2 border-b border-r border-[#f4e4bc]/30"></div>
                                </div>
                                <div v-else class="w-full h-64 flex flex-col items-center justify-center p-8 text-[#8b2635]/60 relative overflow-hidden">
                                     <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/black-loose-fabric.png')]"></div>
                                     <div class="relative z-10 flex flex-col items-center">
                                        <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        <span class="text-[9px] font-cinzel tracking-[0.3em] uppercase opacity-60">Image Corrupted</span>
                                     </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Card -->
                         <div v-if="entry.video" class="bg-[#1e1b19]/60 border border-[#3d352e] p-2 rounded-sm shadow-2xl group hover:border-[#8b2635]/40 transition-all duration-500">
                             <div class="flex items-center justify-between px-4 py-3 border-b border-[#3d352e]/50 mb-2">
                                <div class="flex items-center gap-2 text-[#8c7e6a] group-hover:text-[#f4e4bc] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Moving Record</span>
                                </div>
                                <div class="w-12 h-[1px] bg-[#3d352e] group-hover:bg-[#8b2635]/50 transition-colors"></div>
                            </div>
                            
                            <div class="relative overflow-hidden bg-black/40">
                                <video v-if="!videoError" controls class="w-full opacity-90 hover:opacity-100 transition-opacity" @error="handleVideoError" :poster="defaultVideo">
                                    <source :src="'/storage/' + entry.video">
                                </video>
                                <div v-else class="w-full h-48 flex flex-col items-center justify-center p-8 text-[#8b2635]/60 relative overflow-hidden">
                                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/black-loose-fabric.png')]"></div>
                                    <div class="relative z-10 flex flex-col items-center">
                                        <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        <span class="text-[9px] font-cinzel tracking-[0.3em] uppercase opacity-60">Signal Lost</span>
                                    </div>
                                </div>
                            </div>
                         </div>

                        <!-- Audio Card -->
                        <div v-if="entry.audio" class="bg-[#1e1b19]/60 border border-[#3d352e] p-6 rounded-sm shadow-xl group hover:border-[#8b2635]/40 transition-all duration-500 relative overflow-hidden">
                            <!-- Background waveform hint -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                                <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/sound-waves.png')]"></div>
                            </div>

                            <div class="relative z-10 flex flex-col gap-4">
                                <div class="flex items-center justify-between border-b border-[#3d352e]/50 pb-3">
                                    <div class="flex items-center gap-2 text-[#8c7e6a] group-hover:text-[#f4e4bc] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                        <span class="text-[10px] font-cinzel tracking-[0.2em] uppercase">Voice Echo</span>
                                    </div>
                                    <div class="flex gap-1">
                                        <span class="w-1 h-1 rounded-full bg-[#3d352e] group-hover:bg-[#8b2635] transition-colors"></span>
                                        <span class="w-1 h-1 rounded-full bg-[#3d352e] group-hover:bg-[#8b2635] transition-colors delay-75"></span>
                                        <span class="w-1 h-1 rounded-full bg-[#3d352e] group-hover:bg-[#8b2635] transition-colors delay-150"></span>
                                    </div>
                                </div>

                                <audio v-if="!audioError" controls class="w-full h-10 custom-audio-simple opacity-80 hover:opacity-100 transition-opacity" @error="handleAudioError">
                                    <source :src="'/storage/' + entry.audio">
                                </audio>
                                <div v-else class="flex items-center justify-center py-4 bg-[#000]/20 rounded-sm border border-[#3d352e]/30">
                                    <span class="text-[9px] font-cinzel tracking-[0.3em] uppercase text-[#8b2635]/50">Audio Fragment Damaged</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                 <div class="mt-20 pt-10 border-t border-[#3d352e]/40 flex flex-col items-center gap-6">
                    <div v-if="entry.file">
                        <a :href="'/storage/' + entry.file" target="_blank" class="flex items-center gap-3 text-[#8c7e6a] hover:text-[#f4e4bc] transition-all px-5 py-2 border border-transparent hover:border-[#3d352e] rounded-sm group">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            <span class="text-xs font-cinzel tracking-widest uppercase">Attached Parchment</span>
                        </a>
                    </div>
                    
                    <div class="flex flex-col items-center opacity-40">
                        <div class="flex gap-2 mb-2">
                             <div class="w-1 h-1 rounded-full bg-[#f4e4bc]"></div>
                             <div class="w-1 h-1 rounded-full bg-[#f4e4bc]"></div>
                             <div class="w-1 h-1 rounded-full bg-[#f4e4bc]"></div>
                        </div>
                        <span class="text-[9px] font-cinzel uppercase tracking-[0.5em] text-[#f4e4bc]">Log End</span>
                    </div>
                </div>
            </article>
        </div>
</template>

<style scoped>
.dust-particles {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: 
        radial-gradient(1px 1px at 20px 30px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 40px 70px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 50px 160px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 90px 40px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 130px 80px, #f4e4bc, rgba(0,0,0,0)),
        radial-gradient(1px 1px at 160px 120px, #f4e4bc, rgba(0,0,0,0));
    background-repeat: repeat;
    background-size: 200px 200px;
    animation: dust 60s linear infinite;
}

@keyframes dust {
    from { transform: translateY(0); }
    to { transform: translateY(-200px); }
}

@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

audio.custom-audio-simple {
   height: 32px;
   filter: sepia(0.8) hue-rotate(350deg) saturate(0.8);
}
</style>
