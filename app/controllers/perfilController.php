<?php
class perfilController extends BaseController{

    public function update(){
        $datos  =	 Input::get('data');
        $dateNow = date("Y-m-d");
        if(!Auth::User()->hasRole('hijo') && !Auth::User()->hasRole('hijo_free') && !Auth::User()->hasRole('demo_hijo')){
            $date_min =strtotime("-18 year",strtotime($dateNow));
        }else{
             $date_min =strtotime("-4 year",strtotime($dateNow));
        }
        $date_min=date("Y-m-d",$date_min);
        $rules= [
            "username_persona"          =>"required|user_check|max:50",
            "password_new"              =>"min:8|max:100",
            "cpassword_new"             =>"same:password_new",
            "nombre_persona"            =>"required|letter|max:50",
            "apellido_paterno_persona"  =>"required|letter|max:30",
            "apellido_materno_persona"  =>"required|letter|max:30",
            "sexo_persona"              =>"required|string|size:1",
            "fecha_nacimiento_persona"  =>"required|date_format:Y-m-d|before:$date_min"
        ];
        $messages = [
               "required"    =>  "Este campo :attribute es requerido",
               "alpha"       =>  "Solo puedes ingresar letras",
               "before"      =>  "La fecha que ingresaste tiene que ser menor al $date_min",
               "date"        =>  "Formato de fecha invalido",
               "numeric"     =>  "Solo se permiten digitos",
               "email"       =>  "Ingresa un formato de correo valido",
               "unique"      =>  "Este usuario ya existe",
               "integer"     =>  "Solo se permiten numeros enteros",
               "exists"      =>  "El campo :attribute no existe en el sistema",
               "unique"      =>  "El campo :attribute no esta disponible intente con otro valor",
               "integer"     =>  "Solo puedes ingresar numeros enteros",
               "same"        =>  "Las contraseñas no coinciden",
               "after"       =>  "La fecha de expiracion es incorrecta, no puedes ingresar fechas inferiores al día de hoy",
         ];
        if(!Hash::check($datos["password_persona"],Auth::user()->password)){
            return Response::json("contraseña incorrecta");
        }
        $valid = Validator::make($datos,$rules,$messages);
        if($valid->fails()){
            return $valid->messages();
        }else{
            $datos["password"]=Hash::make($datos["password_new"]);
            $datos["username"]=$datos["username_persona"];
            $user =User::find(Auth::user()->id);
            $user->update($datos);
            $persona = $user->persona();
            $datos_persona = [
               "nombre"            =>  $datos["nombre_persona"],
               "apellido_paterno"  =>  $datos["apellido_paterno_persona"],
               "apellido_materno"  =>  $datos["apellido_materno_persona"],
               "sexo"              =>  $datos["sexo_persona"],
               "fecha_nacimiento"  =>  $datos["fecha_nacimiento_persona"]
           ];
            $persona->update($datos_persona);
            $rolee = Auth::user()->roles[0]->name;
            if ($rolee == "padre" || $rolee == "padre_free" || $rolee == "demo_padre"){
                $padreId = Auth::user()->persona()->first()->padre()->first()->id;
                $padre = padre::where('id', '=', $padreId)->first();
                $padre->update($datos);
            }
            return Response::json("bien");
        }
    }
    public function updateUser(){
        $datos  =    Input::get('data');
        $dateNow = date("Y-m-d");
        if(!Auth::User()->hasRole('hijo') && !Auth::User()->hasRole('hijo_free') && !Auth::User()->hasRole('demo_hijo')){
            $date_min =strtotime("-18 year",strtotime($dateNow));
        }else{
             $date_min =strtotime("-4 year",strtotime($dateNow));
        }
        $date_min=date("Y-m-d",$date_min);
        $rules= [
            "username_persona"          =>"required|user_check|max:50",
            "nombre_persona"            =>"required|letter|max:50",
            "apellido_paterno_persona"  =>"required|letter|max:30",
            "apellido_materno_persona"  =>"required|letter|max:30",
            "sexo_persona"              =>"required|string|size:1",
            "fecha_nacimiento_persona"  =>"required|date_format:Y-m-d|before:$date_min"
        ];
        $messages = [
               "required"    =>  "Este campo :attribute es requerido",
               "alpha"       =>  "Solo puedes ingresar letras",
               "before"      =>  "La fecha que ingresaste tiene que ser menor al $date_min",
               "date"        =>  "Formato de fecha invalido",
               "numeric"     =>  "Solo se permiten digitos",
               "email"       =>  "Ingresa un formato de correo valido",
               "unique"      =>  "Este usuario ya existe",
               "integer"     =>  "Solo se permiten numeros enteros",
               "exists"      =>  "El campo :attribute no existe en el sistema",
               "unique"      =>  "El campo :attribute no esta disponible intente con otro valor",
               "integer"     =>  "Solo puedes ingresar numeros enteros",
               "same"        =>  "Las contraseñas no coinciden",
               "after"       =>  "La fecha de expiracion es incorrecta, no puedes ingresar fechas inferiores al día de hoy",
         ];
        $valid = Validator::make($datos,$rules,$messages);
        if($valid->fails()){
            return $valid->messages();
        }else{

            $user =User::find(Auth::user()->id);
            $datos["username"]=$datos["username_persona"];
            $user->update($datos);
            $persona= $user->persona();
            $datos_persona = [
                "nombre"            =>  $datos["nombre_persona"],
                "apellido_paterno"  =>  $datos["apellido_paterno_persona"],
                "apellido_materno"  =>  $datos["apellido_materno_persona"],
                "sexo"              =>  $datos["sexo_persona"],
                "fecha_nacimiento"  =>  $datos["fecha_nacimiento_persona"]
            ];
            $persona->update($datos_persona);
            $rolee = Auth::user()->roles[0]->name;
            if ($rolee == "padre" || $rolee == "padre_free" || $rolee == "demo_padre"){
                $padreId = Auth::user()->persona()->first()->padre()->first()->id;
                $padre = padre::where('id', '=', $padreId)->first();
                $padre->update($datos);
            }
            return Response::json("bien");
        }
    }
    public function cutImage(){
      $x = round(Input::get("x"));
      $y = round(Input::get("y"));
      $width = round(Input::get("w"));
      $height = round(Input::get("h"));

      if(Input::hasFile('image')){// si se establecio una imagen para recortar
        return $image = Input::file('image');
        $imagen = Image::make($image->getRealPath());
        $alto = $imagen->height;
        $ancho = $imagen->width;

        //guardar imagen original
        $imagen->save(public_path()."/packages/images/perfil/original/".Auth::user()->username.".".$image->getClientOriginalExtension());
        $imagen->crop($width,$height,$x,$y);
        $imageSave =public_path()."/packages/images/perfil/".Auth::user()->username.".".$image->getClientOriginalExtension();

        $imagen->save($imageSave);

        $perfil =Auth::user()->perfil()->first();
        $perfil->foto_perfil=Auth::user()->username.".".$image->getClientOriginalExtension();
        $perfil->save();
        return asset(str_replace(public_path(),"",$imageSave).'?'.$v=rand());
     }else{
        $imagen = Image::make(public_path().'/packages/images/perfil/original/'.Auth::user()->perfil()->first()->foto_perfil);
        $imagen->crop($width,$height,$x,$y);
        $path= public_path().'/packages/images/perfil/'.Auth::user()->perfil()->first()->foto_perfil;
        $imagen->save($path);
        return asset(str_replace(public_path(),"",$path).'?'.$v=rand());
      }
    }

}
