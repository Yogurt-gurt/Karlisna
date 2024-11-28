@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Berita</h1>
    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}" required>
    </div>
    
    <div>
        <label for="content">Content:</label>
        <textarea name="content" id="content" required>{{ old('content') }}</textarea>
    </div>

    <div>
        <label for="image">Image:</label>
        <input type="file" name="image" id="image">
    </div>

    <button type="submit">Save</button>
</form>

</div>
@endsection
