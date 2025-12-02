@extends('layouts.app')

@section('content')
<h2>Configuración de VLANs</h2>
<a href="{{ route('vlans.create') }}" class="btn btn-primary mb-3">Nueva VLAN</a>
<a href="#" onclick="cargarVlansOlt()" class="btn btn-secondary mb-3">
    Cargar VLANs desde OLT
</a>

<div id="oltVlans"></div>

<script>
function cargarVlansOlt() {
    fetch('/api/olt/ssh/vlan/1/list')
        .then(r => r.json())
        .then(resp => {
            if (!resp || resp.success !== true) {
                document.getElementById('oltVlans').innerHTML = '<div class="alert alert-danger">No se pudieron obtener VLANs desde la OLT.</div>';
                return;
            }

            const data = resp.data || [];
            if (data.length === 0) {
                document.getElementById('oltVlans').innerHTML = '<div class="alert alert-info">No se detectaron VLANs en la OLT.</div>';
                return;
            }

            let html = '<h4>VLANs detectadas en la OLT</h4>';
            html += '<table class="table table-sm table-bordered"><thead><tr>' +
                    '<th>#</th><th>Tipo</th><th>Atributo</th><th>STND-Port</th><th>SERV-Port</th><th>VLAN-Con</th>' +
                    '</tr></thead><tbody>';

            data.forEach(v => {
                html += `<tr>` +
                        `<td>${v.number ?? ''}</td>` +
                        `<td>${v.type ?? ''}</td>` +
                        `<td>${v.attribute ?? ''}</td>` +
                        `<td>${v.stnd_port_num ?? ''}</td>` +
                        `<td>${v.serv_port_num ?? ''}</td>` +
                        `<td>${v.vlan_con ?? ''}</td>` +
                        `</tr>`;
            });

            html += '</tbody></table>';
            document.getElementById('oltVlans').innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('oltVlans').innerHTML = '<div class="alert alert-danger">Error al conectar con la API.</div>';
        });
}
</script>

<table class="table table-striped">
    <thead>
       
    </thead>
    <tbody>
        @foreach($vlans as $vlan)
        <tr>
            <td>{{ $vlan->id }}</td>
            <td>{{ $vlan->olt->name }}</td>
            <td>{{ $vlan->number }}</td>
            <td>{{ $vlan->type }}</td>
            <td>{{ $vlan->description }}</td>
            <td>
                <a href="{{ route('vlans.show', $vlan) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('vlans.edit', $vlan) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('vlans.destroy', $vlan) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar VLAN?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
