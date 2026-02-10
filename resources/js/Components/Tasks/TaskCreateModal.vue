<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    priority: 'medium',
    due_date: new Date().toISOString().split('T')[0],
});

const submitTask = () => {
    form.post(route('tasks.store'), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};
</script>

<template>
    <Teleport to="body">
        <Transition 
            enter-active-class="transition duration-700 ease-[cubic-bezier(0.2,0,0,1)]" 
            enter-from-class="opacity-0 translate-y-8 scale-[0.98] blur-sm" 
            leave-active-class="transition duration-400 ease-in" 
            leave-to-class="opacity-0 scale-95 blur-sm"
        >
            <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 lg:p-8">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-[#0a0908]/95 backdrop-blur-md" @click="$emit('close')"></div>
                
                <!-- Content -->
                <div class="relative w-full max-w-xl bg-[#141210] border border-[#3d352e]/50 rounded-[2.5rem] shadow-[0_40px_120px_-20px_rgba(0,0,0,0.8)] overflow-hidden group">
                    <!-- Subtle Texture -->
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/black-paper.png')]"></div>
                    
                    <!-- Ambient Glow -->
                    <div class="absolute -top-32 -right-32 w-64 h-64 bg-[#8b2635]/10 blur-[100px] pointer-events-none"></div>
                    <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-[#d9c5a3]/5 blur-[100px] pointer-events-none"></div>

                    <div class="relative z-10 p-10 md:p-14">
                        <!-- Header Section -->
                        <div class="text-center mb-12 relative">
                            <h2 class="text-4xl md:text-5xl font-cinzel font-bold bg-gradient-to-b from-[#f4e4bc] to-[#c1ae8c] bg-clip-text text-transparent mb-4 tracking-wider">
                                New Mandate
                            </h2>
                            <div class="flex items-center justify-center gap-4">
                                <div class="h-[1px] w-12 bg-gradient-to-r from-transparent to-[#8b2635]/40"></div>
                                <p class="text-[9px] uppercase tracking-[0.5em] text-[#8b2635] font-black font-sans opacity-80">Codifying the Ledger</p>
                                <div class="h-[1px] w-12 bg-gradient-to-l from-transparent to-[#8b2635]/40"></div>
                            </div>
                        </div>
                        
                        <form @submit.prevent="submitTask" class="space-y-10">
                            <!-- Task Name -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center px-1">
                                    <label class="text-[10px] uppercase tracking-[0.3em] text-[#8c7e6a] font-bold">The Decree</label>
                                    <span class="h-2 w-2 rounded-full border border-[#8b2635]/30"></span>
                                </div>
                                <div class="relative group">
                                    <textarea 
                                        v-model="form.name"
                                        rows="3"
                                        placeholder="What needs to be done?"
                                        class="w-full bg-black/30 border border-[#3d352e]/40 focus:border-[#8b2635]/60 rounded-2xl px-8 py-5 text-[#d9c5a3] focus:ring-0 transition-all font-serif text-lg md:text-xl placeholder:text-[#3d352e]/60 scrollbar-hide resize-none leading-relaxed"
                                        required
                                        autofocus
                                    ></textarea>
                                    <div class="absolute bottom-4 right-4 h-1 w-1 bg-[#8b2635]/20 rounded-full"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Priority Selection -->
                                <div class="space-y-4">
                                    <label class="text-[10px] uppercase tracking-[0.3em] text-[#8c7e6a] font-bold px-1">Urgency</label>
                                    <div class="flex p-1 bg-black/40 border border-[#3d352e]/30 rounded-2xl gap-1">
                                        <button 
                                            v-for="p in ['low', 'medium', 'high']" 
                                            :key="p"
                                            type="button"
                                            @click="form.priority = p"
                                            :class="[
                                                form.priority === p 
                                                    ? 'bg-[#8b2635] text-[#f4e4bc] shadow-[0_0_20px_rgba(139,38,53,0.3)]' 
                                                    : 'text-[#8c7e6a] hover:bg-white/5',
                                                'flex-1 py-3 rounded-xl text-[9px] uppercase tracking-[0.2em] font-black transition-all active:scale-95 capitalize'
                                            ]"
                                        >
                                            {{ p }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Due Date -->
                                <div class="space-y-4">
                                    <label class="text-[10px] uppercase tracking-[0.3em] text-[#8c7e6a] font-bold px-1">Sealing Date</label>
                                    <div class="relative group">
                                        <input 
                                            v-model="form.due_date"
                                            type="date"
                                            class="w-full bg-black/40 border border-[#3d352e]/40 focus:border-[#8b2635]/60 rounded-2xl px-6 py-3.5 text-[#d9c5a3] focus:ring-0 transition-all dark:[color-scheme:dark] text-[10px] font-black tracking-[0.3em] uppercase cursor-pointer"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-8 flex flex-col sm:flex-row gap-5">
                                <button 
                                    type="submit"
                                    class="flex-[2] relative overflow-hidden group/btn bg-[#f4e4bc] py-5 rounded-2xl transition-all active:scale-[0.98]"
                                    :disabled="form.processing"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:animate-[shimmer_1.5s_infinite]"></div>
                                    <span class="relative z-10 text-[#1c1a18] font-black text-[11px] uppercase tracking-[0.4em]">
                                        {{ form.processing ? 'Sealing...' : 'Confirm Decree' }}
                                    </span>
                                </button>
                                <button 
                                    type="button" 
                                    @click="$emit('close')"
                                    class="flex-1 border border-[#3d352e]/60 text-[#8c7e6a] py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.4em] hover:bg-white/5 hover:text-[#d9c5a3] transition-all"
                                >
                                    Discard
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes shimmer {
    100% { transform: translateX(100%); }
}

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8) sepia(1) saturate(5) hue-rotate(-20deg) brightness(0.9);
    cursor: pointer;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
