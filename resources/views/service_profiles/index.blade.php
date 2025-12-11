@extends('layouts.app')

@section('content')
<h2>Perfiles de Servicio</h2>
<a href="{{ route('service-profiles.create') }}" class="btn btn-success mb-3">Nuevo Perfil de Servicio</a>
<!-- Opción con color personalizado -->
<!-- <a href="{{ route('service-profiles.create') }}" class="btn mb-3" style="background-color:#ff8800;color:#fff;border:none;">Nuevo Perfil de Servicio</a> -->
<a href="#" onclick="cargarServiceProfilesOlt()" class="btn btn-secondary mb-3">
    Cargar Perfiles desde OLT
</a>

<div id="oltServiceProfiles"></div>


    
    <tbody>
        @foreach($serviceProfiles as $profile)
        <tr>
            <td>{{ $profile->id }}</td>
            <td>{{ $profile->olt->name }}</td>
            <td>{{ $profile->name }}</td>
            <td>{{ ucfirst($profile->service) }}</td>
            <td>{{ $profile->eth_ports ?? '-' }}</td>
            <td>{{ optional($profile->vlan)->number ?? '-' }}</td>
            <td>
                <a href="{{ route('service-profiles.show',$profile) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('service-profiles.edit',$profile) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('service-profiles.destroy',$profile) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
function cargarServiceProfilesOlt() {
    fetch('/api/olt/ssh/service-profile/1/list')
        .then(r => r.json())
        .then(resp => {
            if (!resp || resp.success !== true) {
                document.getElementById('oltServiceProfiles').innerHTML = '<div class="alert alert-danger">No se pudieron obtener Service Profiles desde la OLT.</div>';
                return;
            }

            const data = resp.data || [];
            if (data.length === 0) {
                document.getElementById('oltServiceProfiles').innerHTML = '<div class="alert alert-info">No se detectaron Service Profiles en la OLT.</div>';
                return;
            }

            let html = '<h4>Service Profiles detectados en la OLT</h4>';
            html += '<table class="table table-sm table-bordered"><thead><tr>' +
                    '<th>Profile-ID</th><th>Profile-name</th><th>Binding times</th>' +
                    '</tr></thead><tbody>';

            data.forEach(p => {
                html += `<tr>` +
                        `<td>${p.profile_id ?? ''}</td>` +
                        `<td>${p.name ?? ''}</td>` +
                        `<td>${p.binding_times ?? ''}</td>` +
                        `</tr>`;
            });

            html += '</tbody></table>';
            document.getElementById('oltServiceProfiles').innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('oltServiceProfiles').innerHTML = '<div class="alert alert-danger">Error al conectar con la API.</div>';
        });
}
</script>
@endsection
