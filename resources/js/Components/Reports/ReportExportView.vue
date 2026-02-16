<script setup>
import { computed } from 'vue';
import { marked } from 'marked';

const props = defineProps({
    content: String,
    companyName: String,
    report: Object,
    userName: String,
    userRole: String
});

// Clean the content for export (removing redundant AI-generated headers)
const pdfHtmlContent = computed(() => {
    let cleanMd = props.content;
    cleanMd = cleanMd.replace(/^(#\s*)?Weekly Progress Report.*/im, '');
    cleanMd = cleanMd.replace(/^Date Range:.*$/im, '');
    cleanMd = cleanMd.replace(/^Internship Period:.*$/im, '');
    cleanMd = cleanMd.replace(/^Intern:.*$/im, '');
    cleanMd = cleanMd.replace(/^Name:.*$/im, '');
    cleanMd = cleanMd.replace(/^Role:.*$/im, '');
    cleanMd = cleanMd.replace(/^Position:.*$/im, '');
    cleanMd = cleanMd.replace(/^Company:.*$/im, '');
    
    return marked.parse(cleanMd.trim());
});
</script>

<template>
    <div id="report-document" class="hidden-for-export">
        <div style="width: 794px; background-color: #ffffff; color: #000000; font-family: Arial, sans-serif; padding: 20px; box-sizing: border-box;">
            <div class="pdf-content prose prose-sm max-w-none 
                prose-headings:text-black prose-headings:font-bold 
                prose-p:text-black prose-p:leading-relaxed
                prose-li:text-black" 
                v-html="pdfHtmlContent">
            </div>
        </div>
    </div>
</template>

<style scoped>
.hidden-for-export {
    display: none; 
    position: absolute;
    top: -9999px;
    left: -9999px;
}

.pdf-content :deep(h1), .pdf-content :deep(h2), .pdf-content :deep(h3) {
    color: #000 !important;
    margin-top: 1.5em;
    margin-bottom: 0.5em;
}
.pdf-content :deep(p), .pdf-content :deep(li) {
    color: #111 !important;
}
.pdf-content :deep(strong) {
    color: #000 !important;
}
</style>
