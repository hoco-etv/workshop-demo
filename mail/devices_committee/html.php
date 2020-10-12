<p>Ha beunhazen!</p>

<p>Er is weer eens iets kapot:</p>
<table>
    <tr>
        <th>Merk</th>
        <td><?= $model->brand ?></td>
    </tr>
    <tr>
        <th>Naam</th>
        <td><?= $model->name ?></td>
    </tr>
    <tr>
        <th>Type</th>
        <td><?= $model->type ?></td>
    </tr>
    <tr>
        <th>Huidige status</th>
        <td><?= $model->getStatus()['message'] ?></td>
    </tr>
    <tr>
        <th>Opmerkingen</th>
        <td><?= $model->userReport ?></td>
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