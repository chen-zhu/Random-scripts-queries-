# Caption Generate--random script 

##Goal: 
- Rename each file from this format: Job_XXXX.mp4_5823fb160c8346bc82ec90cc4d4472b1.qt to XXXX.qt.text.
- Generate a file named XXXX.smil for each qt file, that contains the contents of the template.smil file (attached to this email.) Replace the "{file_name}" tags in the template file with XXXX when generating the file.
- Replace square brackets that appear in the caption text with parenthesis. 
- Remove caption blocks that only contain the string "[BLANK_AUDIO]".
- Remove the end times on the caption blocks. Each caption consists of a timestamp followed by lines of text, then another timestamp. We want the trailing timestamp removed.
