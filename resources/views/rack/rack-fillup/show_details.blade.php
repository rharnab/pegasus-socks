


<table class="table table-bordered">
	
	<tr>
		<th>No</th>
		<th>Types Name</th>
		<th>Socks Pair</th>
	</tr>

	@php $sl=1; @endphp
	@foreach($get_data as $single_get_data)
		
		<tr>
			<td>{{$sl++}}</td>	
			<td>{{$single_get_data->types_name}}</td>	
			<td>{{$single_get_data->socks_pair}} Pair</td>	
		</tr>

	@endforeach
</table>