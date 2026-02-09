<script setup>
const props = defineProps({
    form: Object,
    formattedDate: String
});

const getPreviewUrl = (file) => {
    if (!file || !(file instanceof File || file instanceof Blob)) return '';
    return window.URL.createObjectURL(file);
};

defineEmits(['submit']);
</script>

<template>
    <div class="flex-1 flex flex-col bg-[#1c1a18] border border-[#3d352e] rounded-[1.5rem] lg:rounded-[2rem] shadow-2xl relative overflow-hidden order-1 lg:order-none">
        <!-- Vellum Background -->
        <div class="absolute inset-0 opacity-[0.04] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/parchment.png')]"></div>
        
        <!-- Floating Header -->
        <div class="px-6 lg:px-10 py-6 lg:py-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 relative z-20 border-b border-[#3d352e]/50 bg-[#1c1a18]/80 backdrop-blur-md">
            <div>
                <p class="text-[9px] uppercase tracking-[0.4em] text-[#8b2635] font-black mb-2">Chronicle of the Day</p>
                <h2 class="text-3xl lg:text-4xl font-black text-[#d9c5a3] italic tracking-tighter">{{ formattedDate }}</h2>
            </div>
            <button 
                class="w-full sm:w-auto group relative px-8 py-3 bg-[#8b2635] text-[#f4e4bc] rounded-xl overflow-hidden shadow-lg transition-all hover:scale-[1.02] active:scale-95 shrink-0"
                @click="$emit('submit')"
            >
                <span class="relative z-10 text-[10px] font-black uppercase tracking-[0.2em]">Seal Record</span>
                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            </button>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto relative z-10 scrollbar-hide px-6 lg:px-10">
            <!-- Writing Workspace -->
            <div class="py-8 lg:py-12 border-b border-[#3d352e]/30">
                <!-- Title Input -->
                <input 
                    v-model="form.title"
                    placeholder="Title of your Journal Entry..."
                    class="w-full bg-transparent border-none focus:ring-0 p-0 text-2xl lg:text-3xl font-black italic text-[#d9c5a3] placeholder:text-[#3d352e] mb-6"
                />
                <div v-if="form.errors.title" class="text-[#8b2635] text-[10px] mb-4 uppercase tracking-widest font-black">
                    {{ form.errors.title }}
                </div>

                <textarea 
                    v-model="form.content"
                    placeholder="Inscribe your thoughts here..."
                    class="w-full bg-transparent border-none focus:ring-0 p-0 text-lg lg:text-xl font-medium leading-[2.2] text-[#d9c5a3] placeholder:text-[#3d352e] resize-none min-h-[300px] lg:min-h-[400px]"
                ></textarea>
            </div>

            <div v-if="form.errors.content" class="text-[#8b2635] text-[10px] mt-2 uppercase tracking-widest font-black p-4">
                {{ form.errors.content }}
            </div>

            <!-- Media Preview Area (Conditional) -->
            <div v-if="form.image || form.video || form.audio || form.file" class="mt-8 space-y-4 animate-in fade-in slide-in-from-bottom-2 duration-500">
                <div class="flex items-center gap-4 mb-4 opacity-40">
                    <span class="text-[9px] uppercase tracking-[0.4em] text-[#8c7e6a] font-black whitespace-nowrap">Manuscript Attachments</span>
                    <div class="h-[1px] w-full bg-[#3d352e]"></div>
                </div>

                <div class="flex flex-wrap gap-4">
                    <!-- Image Preview -->
                    <div v-if="form.image" class="relative group h-24 w-24 bg-black/40 border border-[#3d352e] rounded-xl overflow-hidden shadow-xl shadow-black/40">
                        <img :src="getPreviewUrl(form.image)" class="w-full h-full object-cover opacity-60" />
                        <button @click="form.image = null" class="absolute inset-0 bg-black/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-[9px] text-white font-black uppercase tracking-widest">Remove</span>
                        </button>
                    </div>

                    <!-- Video Preview -->
                    <div v-if="form.video" class="relative group h-24 w-32 bg-black/40 border border-[#b14c5c]/40 rounded-xl flex flex-col items-center justify-center px-2 shadow-xl shadow-black/40">
                        <svg class="w-6 h-6 text-[#8b2635] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <span class="text-[8px] text-[#8c7e6a] font-bold text-center truncate w-full">{{ form.video.name }}</span>
                        <button @click="form.video = null" class="absolute inset-0 bg-black/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                            <span class="text-[9px] text-white font-black uppercase tracking-widest">Remove</span>
                        </button>
                    </div>

                    <!-- Audio Preview -->
                    <div v-if="form.audio" class="relative group h-24 w-32 bg-black/40 border border-[#d9c5a3]/20 rounded-xl flex flex-col items-center justify-center px-2 shadow-xl shadow-black/40">
                        <svg class="w-6 h-6 text-[#d9c5a3] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                        <span class="text-[8px] text-[#8c7e6a] font-bold text-center truncate w-full">{{ form.audio.name }}</span>
                        <button @click="form.audio = null" class="absolute inset-0 bg-black/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                            <span class="text-[9px] text-white font-black uppercase tracking-widest">Remove</span>
                        </button>
                    </div>

                    <!-- File Preview -->
                    <div v-if="form.file" class="relative group h-24 w-32 bg-black/40 border border-[#3d352e] rounded-xl flex flex-col items-center justify-center px-2 shadow-xl shadow-black/40">
                        <svg class="w-6 h-6 text-[#8c7e6a] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        <span class="text-[8px] text-[#8c7e6a] font-bold text-center truncate w-full">{{ form.file.name }}</span>
                        <button @click="form.file = null" class="absolute inset-0 bg-black/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                            <span class="text-[9px] text-white font-black uppercase tracking-widest">Remove</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Refined Media Actions -->
            <div class="mt-8 flex items-center gap-6 pb-12 border-t border-[#3d352e]/20 pt-8">
                <!-- Consolidated Media Button (Image, Video, File) -->
                <div class="flex items-center gap-3">
                    <p class="text-[9px] uppercase tracking-widest text-[#3d352e] font-black">Attachments:</p>
                    <div class="flex gap-2">
                        <!-- Image -->
                        <label class="cursor-pointer p-3 bg-black/40 border border-[#3d352e] rounded-xl text-[#8c7e6a] hover:text-[#bc4749] hover:border-[#8b2635]/50 transition-all group" title="Add Image">
                            <input type="file" @input="form.image = $event.target.files[0]" class="hidden" accept="image/*" />
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </label>
                        <!-- Video -->
                        <label class="cursor-pointer p-3 bg-black/40 border border-[#3d352e] rounded-xl text-[#8c7e6a] hover:text-[#bc4749] hover:border-[#8b2635]/50 transition-all group" title="Add Video">
                            <input type="file" @input="form.video = $event.target.files[0]" class="hidden" accept="video/*" />
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </label>
                        <!-- File -->
                        <label class="cursor-pointer p-3 bg-black/40 border border-[#3d352e] rounded-xl text-[#8c7e6a] hover:text-[#bc4749] hover:border-[#8b2635]/50 transition-all group" title="Add Document">
                            <input type="file" @input="form.file = $event.target.files[0]" class="hidden" />
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </label>
                    </div>
                </div>

                <!-- Separator -->
                <div class="h-8 w-[1px] bg-[#3d352e]"></div>

                <!-- Specialized Audio/Voice Action -->
                <div class="flex items-center gap-3">
                    <p class="text-[9px] uppercase tracking-widest text-[#3d352e] font-black">Dictation:</p>
                    <label class="cursor-pointer flex items-center gap-3 px-5 py-3 bg-[#8b2635]/10 border border-[#8b2635]/20 rounded-xl text-[#8b2635] hover:bg-[#8b2635] hover:text-[#f4e4bc] transition-all group shadow-inner">
                        <input type="file" @input="form.audio = $event.target.files[0]" class="hidden" accept="audio/*" />
                        <svg class="w-4 h-4 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                        <span class="text-[10px] uppercase tracking-[0.2em] font-black">Voice Note</span>
                    </label>
                </div>

                <!-- Status Indicators (Floating) -->
                <div class="ml-auto flex gap-2">
                    <div v-if="form.image || form.video || form.file || form.audio" class="flex items-center gap-2 px-3 py-1 bg-[#d9c5a3]/5 rounded-full border border-[#d9c5a3]/10">
                        <div class="h-1 w-1 rounded-full bg-green-500 animate-ping"></div>
                        <span class="text-[8px] uppercase tracking-widest text-[#d9c5a3]/50 font-bold">Media Linked</span>
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
