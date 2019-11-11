import sys
import csv
from bs4 import BeautifulSoup

#костыль какой-то, забыл уже для чего, хотя написано ниже
maxInt = sys.maxsize
while True:
    # decrease the maxInt value by factor 10
    # as long as the OverflowError occurs.
    try:
        csv.field_size_limit(maxInt)
        break
    except OverflowError:
        maxInt = int(maxInt/10)

def get_text(node):
    pass

def clear_headings(content):
    """
    Чисим содержимое тего h1, h2, h3
    """
    tags_for_clear = ['h1', 'h2', 'h3', 'h4']
    soup = BeautifulSoup(content, 'html.parser')
    for tag_h in tags_for_clear:
        for head1 in soup.findAll(tag_h):
            tag_style = ''
            for child in head1.children:
                if child.name:
                    if 'style' in child.attrs:
                        tag_style += child['style']
                        print(tag_style)
                    head1.find(child.name).replace_with(child.text)
            if tag_style:
                head1['style'] = tag_style
    return soup
"""
index_content = 3 #14 - site_content, 3  - tv content
file_content = 'modx_site_tmplvar_contentvalues.csv'
#file_content = 'modx_site_content.csv'

all_prods = []
with open(file_content, encoding="utf8") as f:
    csv_reader = csv.reader(f, delimiter=';')
    for line in csv_reader:
        line[index_content] = clear_headings(line[index_content])
        all_prods.append(line)

print(all_prods[:10])
with open('new_'+file_content, 'w', encoding="utf8", newline='') as f:
    writer = csv.writer(f, delimiter=';', quoting=csv.QUOTE_ALL)
    for line in all_prods:
        writer.writerow(line)
"""
html_txt0 = "<h2>123</h2>"
html_txt1 = "<h1><a href='123123' style='color: #fff;'>Hello</a> world1</h1>"
html_txt2 = "<h1><span>Hello</span> world2</h1>"
html_txt3 = "<body><h1><a style='color: #fff;' href='123123'>Hello <strong style='background-color: #fff;'>fckng</strong></a> world3</h1><p> yes </p> <div><p><h1><span>trash </span>cleared</h1>test</p></div></body>"
html_txt4 = "<body><h1><a href='123123'>Hello</a> world4</h1><p> yes </p> <h1><a href='1231231'>Hello</a> world4a<span>trash </span></h1></body>"
html_list = [html_txt0, html_txt1, html_txt2, html_txt3, html_txt4]
html_new_list = []

for line in html_list:
    html_new_list.append(clear_headings(line))
print(html_new_list)
