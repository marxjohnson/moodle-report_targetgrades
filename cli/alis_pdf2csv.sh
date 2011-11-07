#!/bin/sh

# Read pages 3 to 13 of the PDF into a plain text file, retaining the table layout
### @export 'pdftotext'
pdftotext -f 3 -l 13 -layout Equations.pdf
### @end

# Lots of passes through sed to format the file so that it can be parsed as a CSV file:
# Passes 1-5: Delimit the table columns with pipes
# Pass 6: Remove column headings from the tables
# Pass 7: Remove Page footers
# Pass 8: Remove Page headers
# Pass 9-10: Remove blank lines and page break lines
# Pass 11: Remove document title
# Passes 12-13: Fix malformatted Informations and Communications Technology lines
### @export 'sed'
sed 's/Art   /Art /' Equations.txt | \
sed 's/[ ]\{2,\}/|/' | \
sed 's/[ ]\{2,\}/|/' | \
sed 's/[ ]\{2,\}/|/' | \
sed 's/[ ]\{2,\}/|/' | \
sed 's/[ ]\{2,\}/|/' | \
sed 's/Subject|.*$//' | \
sed 's/^.*|Page.*$//' | \
sed 's/|Alis - Subject Level Regression Equations [0-9]\{4\}//' | \
sed '//d' | \
sed '/^$/d' | \
sed '1,2d' | \
sed '/^Technolog$/d' | \
sed 's/\(Information And Communications\)/\1 Technology/' > Equations.txt
