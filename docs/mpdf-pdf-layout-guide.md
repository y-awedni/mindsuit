# mPDF PDF Layout Guide

All PDFs are rendered by `src/Service/PdfGenerator.php` using the `mpdf/mpdf` library.
Templates live in `templates/<document>/pdf.html.twig`.
Shared CSS is in `templates/pdf/_styles.css.twig`.

---

## Quick reference — what you can control

| You want to… | How to do it |
|---|---|
| Move a box left / right | Change `width: N%` on the parent `<td>` (societe-col / client-col) |
| Change font size anywhere | `style="font-size: 12px;"` (px, mm, pt all work) |
| Make a box bigger / smaller | `padding` on the `<td>` inside the box |
| Change border color | `border: 1px solid #HEX;` on `.client-box` |
| Colored header strip on a box | `<tr><th style="background:#HEX;">...</th></tr>` |
| Background fill on a cell | `background: #HEX;` |
| Bold / underline text | `<b>`, `<u>`, or `font-weight: bold` in CSS |
| Add a horizontal separator line | `<hr style="border:0; border-top:1px solid #ccc;">` |
| Resize the logo | `<img style="width: NNpx;">` |
| Change page margins | Edit `margin_*` values in `PdfGenerator::newMpdf()` |
| Stripe the lines table | `.lines tr:nth-child(even) td { background: #f6f6f6; }` |

---

## Hard limits — avoid these in mPDF templates

- **No CSS flexbox or grid** — use `<table>` for layout instead
- **No `<fieldset>` / `<legend>`** — collapses to a single line (mPDF bug)
- **No `transform: rotate()`** — silently ignored
- **No `position: absolute/fixed`** in body blocks — use `WriteFixedPosHTML` via PHP (footer already uses this)

---

## Standard document structure

Every `pdf.html.twig` follows this skeleton:

```twig
{% block body %}
    {# 1. Header row: logo | doc title | date #}
    <table class="header"> ... </table>

    {# 2. Parties row: société info LEFT | client/fournisseur box RIGHT #}
    <table class="parties">
        <tr>
            <td class="societe-col"> ... </td>
            <td class="client-col">
                <table class="client-box">
                    <tr><th>Code client/fournisseur : XXX</th></tr>
                    <tr><td> ... details ... </td></tr>
                </table>
            </td>
        </tr>
    </table>

    {# 3. Lines table #}
    <table class="lines"> ... </table>
{% endblock %}

{% block footer %}
    {# Pinned to the bottom of the last page by PdfGenerator #}
    <table class="summary"> ... </table>
    <table class="signatures"> ... </table>
{% endblock %}
```

---

## The client/fournisseur box pattern

**Always use a nested `<table>`** — never `<div>` or `<fieldset>`.

```twig
<table class="client-box">
    <tr>
        <th>Code client : {{ doc.client.code }}</th>
    </tr>
    <tr>
        <td>
            {% if doc.client.rs %}<b>Client : </b>{{ doc.client.rs }}<br>{% endif %}
            {% if doc.client.adresse %}<b>Adresse : </b>{{ doc.client.adresse }}<br>{% endif %}
            {% if doc.client.tel %}<b>Tél : </b>{{ doc.client.tel }}<br>{% endif %}
            {% if doc.client.email %}<b>Email : </b>{{ doc.client.email }}<br>{% endif %}
            {% if doc.client.mf %}<b>M.F : </b>{{ doc.client.mf }}{% endif %}
        </td>
    </tr>
</table>
```

CSS in `_styles.css.twig`:
```css
.parties .societe-col { width: 55%; padding: 4px 12px 4px 0; }
.parties .client-col  { width: 45%; padding: 4px 0; }
.client-box           { border: 1px solid #555; line-height: 1.55; }
.client-box th        { background: #f0f0f0; border-bottom: 1px solid #555;
                        padding: 5px 8px; text-align: left; font-weight: bold; font-size: 12px; }
.client-box td        { padding: 6px 8px; font-size: 11px; }
```

---

## Templates by document

| Document | Template | Entity var | Party type |
|---|---|---|---|
| Bon de Livraison | `bonlivraison/pdf.html.twig` | `bonlivraison` | client |
| Facture | `facture/pdf.html.twig` | `facture` | client |
| Devis | `devis/pdf.html.twig` | `devis` | client |
| Avoir | `factureavoir/pdf.html.twig` | `factureAvoir` | client (via `factureAvoir.facture.client`) |
| Bon de Réception | `bonreception/pdf.html.twig` | `bonreception` | fournisseur |
| Bon de Commande Frs | `boncommandefrs/pdf.html.twig` | `bonCommandeFrs` | fournisseur |
