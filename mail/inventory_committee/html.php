<p>Ha beunhazen!</p>

<p>Er is weer eens een component op:</p>
<table>
    <tr>
        <th>Categorie</th>
        <td><?= $model->category ?></td>
    </tr>
    <tr>
        <th>Naam</th>
        <td><?= $model->name ?></td>
    </tr>
    <tr>
        <th>Info</th>
        <td><?= $model->info ?></td>
    </tr>
    <tr>
        <th>Opmerkingen</th>
        <td><?= $model->additionalNotes ?></td>
    </tr>
</table>

<p>Jullie weten wat je te doen staat.</p>

<p>Klusjes en een dikke lebber van de site!</p>

<style>
    th {
        text-align: right;
        padding-right: 25px;
        vertical-align: top;
    }
</style>