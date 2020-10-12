<p>Ha beunhazen!</p>

<p>Er is een nieuwe bestelling geplaatst en bevestigd:</p>
<table>
    <tr>
        <th>Besteld door</th>
        <td><?= $models[0]->customer->name ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $models[0]->customer->email ?></td>
    </tr>
    <tr>
        <th>Besteld op</th>
        <td><?= $models[0]->date ?></td>
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