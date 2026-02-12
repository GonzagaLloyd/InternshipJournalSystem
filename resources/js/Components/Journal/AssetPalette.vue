<script setup>
const props = defineProps({
    form: Object
});

const handleFileSelect = (type, event) => {
    props.form[type] = event.target.files[0];
};

const assetTypes = [
    { type: 'image', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { type: 'video', icon: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' },
    { type: 'file', icon: 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13' },
    { type: 'audio', icon: 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z' }
];
</script>

<template>
    <div class="flex flex-col items-center justify-center gap-2 md:gap-3 py-4 px-2 bg-black/40 backdrop-blur-xl rounded-l-2xl border-l border-y border-white/5 shadow-2xl translate-x-8 hover:translate-x-0 transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] group/palette">
        <label v-for="asset in assetTypes" :key="asset.type" 
            class="group/asset relative cursor-pointer p-3.5 text-[#A68B6A]/50 hover:text-[#E3D5C1] transition-all duration-300 active:scale-90"
            :title="`Attach ${asset.type}`"
        >
            <input type="file" @input="handleFileSelect(asset.type, $event)" class="hidden" :accept="asset.type === 'image' ? 'image/*' : asset.type === 'video' ? 'video/*' : asset.type === 'audio' ? 'audio/*' : '*'" />
            <svg class="w-5 h-5 relative z-10 group-hover/asset:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="asset.icon"></path>
            </svg>
            <div class="absolute inset-0 bg-white/5 opacity-0 group-hover/asset:opacity-100 rounded-xl transition-opacity"></div>
        </label>
    </div>
</template>
