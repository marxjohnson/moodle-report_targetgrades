#!/bin/sh

# Read pages 2 to 10 of the PDF into a plain text file, retaining the table layout
pdftotext -f 2 -l 10 -layout Equations.pdf

# Lots of passes through sed to format the file so that it can be parsed as a CSV file:
# Passes 1-5: Delimit the table columns with pipes
# Pass 6: Remove column headings from the tables
# Pass 7: Remove Page footers
# Pass 8: Remove Page headers
# Pass 9: Remove blank lines and page break lines
# Pass 10: Remove document title
# Passes 11-12: Fix malformatted Informations and Communications Technology lines
sed 's/Art   /Art /' Equations.txt | sed 's/[ ]\{2,\}/|/' | sed 's/[ ]\{2,\}/|/' | sed 's/[ ]\{2,\}/|/' | sed 's/[ ]\{2,\}/|/' | sed 's/[ ]\{2,\}/|/' | sed 's/Subject|.*$//' | sed 's/^.*|Page.*$//' | sed 's/|Alis - Subject Level Regression Equations [0-9]\{4\}//' | sed '/^$/d' | sed '1,2d' | sed '/^Technolog$/d' | sed 's/\(Information And Communications\)/\1 Technology/' > Equations.txt