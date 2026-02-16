import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from '@/Composables/useToast';

const isGenerating = ref(false);
const generationStep = ref(0);
const generationError = ref(null);
const resultData = ref(JSON.parse(localStorage.getItem('report_generation_results') || 'null'));
const isMinimized = ref(false);
const currentJobId = ref(localStorage.getItem('report_generation_job_id'));

let pollInterval = null;
let stepInterval = null;

// Helper for local cleanup without triggering storage event again
function clearStatusLocally() {
    isGenerating.value = false;
    generationStep.value = 0;
    resultData.value = null;
    generationError.value = null;
    currentJobId.value = null;
    stopTimers();
}

function stopTimers() {
    if (pollInterval) clearInterval(pollInterval);
    if (stepInterval) clearInterval(stepInterval);
    pollInterval = null;
    stepInterval = null;
}

async function pollJobStatus(jobId) {
    if (!jobId || typeof route !== 'function') return;

    try {
        const response = await axios.get(route('reports.job.status', jobId));

        if (response.data.status === 'completed') {
            stopTimers();

            // Persist results so they survive refresh
            localStorage.setItem('report_generation_results', JSON.stringify(response.data));

            // Atomic update
            resultData.value = response.data;
            generationStep.value = 4;
            isGenerating.value = false;
        } else if (response.data.status === 'failed') {
            stopTimers();

            generationError.value = response.data.error || 'Report generation failed.';
            generationStep.value = 0;
            isGenerating.value = false;
            localStorage.removeItem('report_generation_job_id');
            localStorage.removeItem('report_generation_results');
        } else {
            // Still in progress
            if (!isGenerating.value && generationStep.value !== 4) {
                isGenerating.value = true;
                // If it's a fresh reload and we don't know the step, default to 1
                if (generationStep.value === 0) generationStep.value = 1;
            }
        }
    } catch (err) {
        if (err.response && err.response.status === 404) {
            clearStatusLocally();
            localStorage.removeItem('report_generation_job_id');
            localStorage.removeItem('report_generation_results');
        }
    }
}

// Initial/Focus Sync
const performSync = () => {
    const storedJobId = localStorage.getItem('report_generation_job_id');
    const storedResults = localStorage.getItem('report_generation_results');

    // 1. Handle results existing in storage
    if (storedResults) {
        resultData.value = JSON.parse(storedResults);
        generationStep.value = 4;
        isGenerating.value = false;
        stopTimers();
        return;
    }

    // 2. Handle no job in progress
    if (!storedJobId) {
        if (isGenerating.value || generationStep.value !== 0) {
            clearStatusLocally();
        }
        return;
    }

    // 3. Handle active job
    currentJobId.value = storedJobId;

    // Resume polling if needed
    if (!pollInterval && (generationStep.value < 4 || resultData.value === null)) {
        // Force immediate visual state if we lost it
        if (generationStep.value === 0) {
            isGenerating.value = true;
            generationStep.value = 1;
            isMinimized.value = true;
        }

        pollJobStatus(storedJobId);
        pollInterval = setInterval(() => {
            pollJobStatus(currentJobId.value);
        }, 3000);
    }
};

// Listen for cross-tab items
window.addEventListener('storage', (event) => {
    if (event.key === 'report_generation_job_id' || event.key === 'report_generation_results') {
        performSync();
    }
});

// Sync on visibility
if (typeof document !== 'undefined') {
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            performSync();
        }
    });
}

// Initial Kickoff
setTimeout(performSync, 100);

export function useReportGeneration() {
    const { success, error: toastError } = useToast();

    // Ensure sync on ogni composable call (mount)
    onMounted(() => {
        performSync();
    });

    const clearStatus = () => {
        clearStatusLocally();
        localStorage.removeItem('report_generation_job_id');
        localStorage.removeItem('report_generation_results');
    };

    const weaveFragments = async (entryIds) => {
        if (!entryIds || entryIds.length === 0) return;

        localStorage.removeItem('report_generation_job_id');
        localStorage.removeItem('report_generation_results');

        isGenerating.value = true;
        generationStep.value = 1;
        generationError.value = null;
        resultData.value = null;
        isMinimized.value = true;

        stopTimers();
        stepInterval = setInterval(() => {
            if (generationStep.value < 3) {
                generationStep.value++;
            }
        }, 4000);

        try {
            const response = await axios.post(route('reports.generate'), { entry_ids: entryIds });

            if (response.data.job_id) {
                currentJobId.value = response.data.job_id;
                localStorage.setItem('report_generation_job_id', response.data.job_id);

                performSync(); // Kick off the polling/sync logic
            }
        } catch (err) {
            const errMsg = err.response?.data?.error || 'The Grand Scribe is silent...';
            stopTimers();
            isGenerating.value = false;
            generationError.value = errMsg;
            toastError(errMsg, 8000);
        }
    };

    return {
        isGenerating,
        generationStep,
        generationError,
        resultData,
        isMinimized,
        currentJobId,
        clearStatus,
        weaveFragments,
        // Helper to force sync manually if needed
        sync: performSync
    };
}
