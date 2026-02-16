/**
 * Markdown Processor Utility
 * Handles conversion between raw markdown and block-based visual structures
 */

export const cleanUpMarkdown = (text) => {
    if (!text) return '';
    let clean = text;

    // 1. Convert standalone bold lines to H2 headers (e.g. **1. Executive Summary**)
    clean = clean.replace(/^\s*\*\*(.*?)\*\*\s*$/gm, '## $1');

    // 2. Fix "1. **Title**" pattern to "## 1. Title"
    clean = clean.replace(/^\s*(\d+\.)\s*\*\*(.*?)\*\*\s*$/gm, '## $1 $2');

    // 3. Ensure H1 has proper spacing
    clean = clean.replace(/^#\s+(.*?)$/gm, '# $1');

    // 4. Remove bolding from keys like "**Role:**" -> "Role:"
    clean = clean.replace(/\*\*(.*?):\*\*/g, '$1:');

    // 5. Convert list bullet consistency
    clean = clean.replace(/^(\s*)\*\s/gm, '$1- ');

    return clean;
};

export const parseMarkdownToBlocks = (md) => {
    if (!md) return [];
    const lines = md.split('\n');
    const result = [];

    lines.forEach((line) => {
        const trimmed = line.trim();
        if (line.startsWith('# ')) {
            result.push({ type: 'h1', content: line.substring(2) });
        } else if (line.startsWith('## ')) {
            result.push({ type: 'h2', content: line.substring(3) });
        } else if (line.startsWith('### ')) {
            result.push({ type: 'h3', content: line.substring(4) });
        } else if (trimmed.startsWith('- ') || trimmed === '-') {
            // Handle "- " or just "-" as an empty list item
            const content = trimmed === '-' ? '' : line.substring(line.indexOf('-') + 2);
            result.push({ type: 'li', content: content });
        } else if (line === ' ' || line === '  ') {
            // Specific case for manual spacer/placeholder paragraphs
            result.push({ type: 'p', content: '' });
        } else if (trimmed === '') {
            result.push({ type: 'br', content: '' });
        } else {
            result.push({ type: 'p', content: line });
        }
    });

    // Filter consecutive breaks
    return result.filter((b, i, arr) => {
        if (b.type === 'br' && i > 0 && arr[i - 1].type === 'br') return false;
        return true;
    });
};

export const blocksToMarkdown = (blockList) => {
    return blockList.map(b => {
        if (b.type === 'h1') return `# ${b.content}`;
        if (b.type === 'h2') return `## ${b.content}`;
        if (b.type === 'h3') return `### ${b.content}`;
        if (b.type === 'li') return `- ${b.content}`;
        if (b.type === 'br') return '';
        return b.content;
    }).join('\n');
};
