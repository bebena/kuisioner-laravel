@extends('layouts.app')

@section('css')
<style>
    #tanda-tangan .tanda-tangan-wrapper {
        position: relative;
        width: 100%;
        height: 300px;
    }

    .tanda-tangan-wrapper .hapus-ttd {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    #tanda-tangan canvas {
        width: inherit;
        height: inherit;
    }

    @media(max-width: 768px) {
        #tanda-tangan .tanda-tangan-wrapper {
            width: 100%;
            height: 200px;
        }
        /* #tanda-tangan canvas {
            width: 100%;
            height: 400px;
            border: 1px solid black;
        } */
    }

    #questionnaire-wrapper {
        width: 900px;
        margin: 20px auto;
        padding: 15px;
    }

    @media(max-width: 900px) {
        #questionnaire-wrapper {
            width: 100%;
        }
    }

    #questionnaire-wrapper h3 {
        text-align: center
    }

    #questionnaire-wrapper .pertanyaan-wrapper {
        margin-top: 40px;
    }

    .pertanyaan-text {
        display: inline-block;
        font-size: 1.2em;
    }
</style>
@endsection

@section('content')
    <div class="container bg-white p-5">
        <div class="w-full mb-5">
            <h3 class="text-xl font-bold text-center">{{ $kategori->judul }}</h3>
        </div>

        <hr>

        {!! Form::open(['url' => route('survey.jawab'), 'id' => 'jawab-survey']) !!}
        {!! Form::hidden('questionnaire_id', $id) !!}

        <div class="w-full max-w-sm mt-5">
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-2/3">
                    <label class="block text-black md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    Nama Lengkap
                    </label>
                </div>
                <div class="md:w-2/3">
                    {!! Form::text('nama_lengkap', '', ['class' => 'bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500']) !!}
                </div>
            </div>
            <div class="md:flex md:items-center mb-6">
                <div class="md:w-2/3">
                    <label class="block text-black md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    Guru Mata Pelajaran
                    </label>
                </div>
                <div class="md:w-2/3">
                    {!! Form::text('add_info', '', ['class' => 'bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500']) !!}
                </div>
            </div>
        </div>

        <div class="pertanyaan-wrapper form-horizontal">

            <?php $num = 1; ?>
            <table class="">
            @foreach ($pertanyaans as $pertanyaan)
            <tr class="mb-4">
                <td style="vertical-align: top; font-size: 1.2em" width="20px"><p class="text-base">{{ $num }}.</p></td>
                <td>
                    <div class="text-base mb-2">
                        {!! $pertanyaan->pertanyaan !!}
                    </div>
                    <div class="pertanyaan-item-option">
                        <ul>
                        @foreach ($questionOptions as $question)
                            <li class="text-base mb-2">{!! Form::radio('jawaban['.  $pertanyaan->id .']', $question->id, false, ) !!}
                            <span>{!! Form::label('pertanyaan_' . $pertanyaan->id , $question->title, []) !!}</span></li>
                        @endforeach
                        </ul>
                    </div>
                </td>
            </tr>
            <?php $num++; ?>
            @endforeach
        </table>
        </div>
        <div id="tanda-tangan" class="mt-10">
            <div class="text-base font-bold">Tanda Tangan</div>
            <div class="tanda-tangan-wrapper border border-gray-500 rounded-md mt-5">
                <a href="javascript:void(0)" onclick="deletesignature()" class="btn hapus-ttd">Hapus</a>
                <canvas></canvas>
            </div>
        </div>
        <div class="submit">
            <button class="w-full hover:bg-green-300 bg-green-500 mt-2 px-3 py-2 rounded-md text-white">SIMPAN</button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('js')
<script src="{{ asset('plugins/signature_pad/js/signature_pad.js') }}"></script>
<script>
    var wrapper = document.getElementById("tanda-tangan");
    var canvas = wrapper.querySelector("canvas");
    var signaturePad = new SignaturePad(canvas, { });

    function resizeCanvas() {
        var ratio =  Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // otherwise isEmpty() might return incorrect value
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    function deletesignature() {
        signaturePad.clear();
    }
</script>
<script>
    $(function() {
        $('#jawab-survey').submit(function(e) {
            e.preventDefault();
            var signature = saveSignature();
            if (signature) {
                var data = $(this).serializeArray();

                var formdata = new FormData();
                for (var i = 0; i < data.length; i++) {
                    formdata.append(data[i].name, data[i].value);
                }
                // $.each(data, function(el) {
                    
                // });
                formdata.append('signature', signature);

                $.ajax({
                    url: '{{ route('survey.jawab') }}',
                    data: formdata,
                    type: 'post',
                    processData: false,
                    contentType: false
                }).done(function(data) {
                    console.log(data);
                }).fail(function(data) {

                });
            } else {
                /* do nothing */
            }
        })
    });

    function saveSignature() {
        if (signaturePad.isEmpty()) {
            alert("Please provide a signature first.");
            return null;
        } else {
            var dataURL = signaturePad.toDataURL();
            return dataURL;
        }
    }
</script>
@endsection