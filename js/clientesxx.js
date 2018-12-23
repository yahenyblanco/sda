 
 function llenardatalist(){
    $.post( '/gestionandamios/pagos/dt2', function(mensaje) {
      $("#txt3").html(mensaje);
        for (var i=0, total = mensaje.length; i < total; i++) {
          $("#categoryname2").append("<option value="+mensaje[i]['razonsocial']+">"+mensaje[i]['razonsocial']+"</option>");
        }
    });

  }
   function modificardetalles() {
        var tp='Normal';
          $("input[type=checkbox]:checked").each(function(){
            tp='Restringido';
          });
          var razon= document.getElementById('dtrazon').value;
          var razonrestriccion= document.getElementById('dtrazonrest').value;
           fiscal= document.getElementById('dtdomicilio').value;
           ciudad= document.getElementById('dtciudad').value;
           comentarios= document.getElementById('dtcomentarios').value;
           correo = document.getElementById('dtcorreo').value;
           tl1 = document.getElementById('dttl1').value;
           tl2 = document.getElementById('tdtl2').value;
           cliente = document.getElementById('dtcliente').value;
           rfc = document.getElementById('dtrfc').value;
            var statusConfirm =(confirm("Desea Enviar las Modificaciones?"));
            if (statusConfirm == true){ 
              $.post("/sda/clientes/dt", {cliente:cliente,fiscal:fiscal,ciudad:ciudad,comentarios:comentarios,correo:correo,tlf1:tl1,tlf2:tl2,rest:tp,razon:razon,rfc:rfc,razonrestriccion:razonrestriccion}, function(mensaje) {
                 $("#inp11").html(mensaje);
              });
               document.getElementById('dtdomicilio').value=fiscal;
               document.getElementById('dtciudad').value=ciudad;
               document.getElementById('dtcomentarios').value=comentarios;
               document.getElementById('dtcorreo').value=correo;
               document.getElementById('dttl1').value=tl1;
               document.getElementById('tdtl2').value=tl2;
               alert('Cliente Actualizado');
               location.reload();
             }else{
              alert('Procedimiento Cancelado');
             }
    }
     function cerrardetallescliente() {
            location.reload();
     }
      function modificarcta() {
            var statusConfirm =(confirm("Desea Enviar las Modificaciones?"));
            if (statusConfirm == true){ 
            var comentarios= document.getElementById('ctacoment').value;
            var ctaid2= document.getElementById('ctaid').value;
            var domicilio = document.getElementById('cuentadom').value;
            var clienteid = document.getElementById('clienteid').value;
            var dtcliente = document.getElementById('dtcliente').value;
             if (document.getElementById("chekcta").checked==true){
               var tp='Inactiva';
              }else{
                var tp='Normal';
              }
             $.post("/sda/clientes/modificarcta", {dtcliente:dtcliente,clienteid:clienteid,ctaact:cuent, ctaid:ctaid2,domicilio:domicilio,comentarios:comentarios,status:tp}, function(mensaje) {
                 $("#inp11").html(mensaje);
              });
              alert('Obra Actualizada');
              location.reload()
           }else{
            alert('Procedimiento Cancelado');
           }
      }
       function crearcta2() {
          var statusConfirm =(confirm("Esta Seguro de Agregar esta Obra?"));
          if (statusConfirm == true){ 
              var cliente=document.getElementById('dtcliente').value;
              document.getElementById('clienteid').value=cliente;
              var cuentaid= document.getElementById('ctaid').value;
              var comentarios= document.getElementById('ctacoment').value;
              var domicilio = document.getElementById('cuentadom').value;
              document.getElementById('btnmodificarcta').disabled=true;
              if (document.getElementById("chekcta").checked==true){
               var tp='Inactiva';
              }else{
                var tp='Normal';
              }

              if(domicilio==''){
                alert('Todos los Campos deben estar Completos');
              }else{
                 $.post("/sda/clientes/regcuenta", {clienteid:clienteid,cuentaid:cuentaid,comentarios:comentarios,domicilio:domicilio,status:tp}, function(mensaje) {
                 $("#inp11").html(mensaje);
              });
                alert('Obra Registrada');
                $("#tabla_cuentas").append("<tr class='dev'><tr class='dev'><td style='text-align: center';><a href=#myModal_cuenta data-toggle=modal data-target=#myModal_cuenta >Editar</td><td style='text-align: center'>"+cuentaid+"</td><td style='text-align: center'>"+comentarios+"</td><td style='text-align: center'>"+domicilio+"</td><td style='text-align: center'>"+tp+"</td></tr>");
          }
        }else{
            alert('Procedimiento Cancelado');
            $('#myModal_cuenta').modal('hide');
          }
      }
      function registrar() {
              var cliente= document.getElementById('inp1').value;
              var razon= document.getElementById('inp2').value;
              var rfc= document.getElementById('inp3').value;
              var domicilio = document.getElementById('inp4').value;
              var ciudad = document.getElementById('inp5').value;
              var comentarios = document.getElementById('inp6').value;
              var correo = document.getElementById('inp7').value;
              var razonrest = document.getElementById('inp10').value;

              if (document.getElementById("chek").checked==true){
                tp='Restringido';
              }else{
                 tp='Normal';
              }
              var tlf1 = document.getElementById('inp9').value;
              var tlf2 = document.getElementById('inp11').value;
               if(razon=='' && rfc=='' && domicilio=='' && ciudad==''){
                alert('Debe Completar todos los Campos');
               }else{
                if(document.getElementById('inp3').value==''){
                  rfc='No Ingresado';
                } 

                $.post("/sda/clientes/reg", {cliente:cliente,razon:razon,rfc:rfc,domicilio:domicilio,ciudad:ciudad,comentarios:comentarios,correo:correo,estado:tp,telf1:tlf1,telf2:tlf2,razonrest:razonrest}, function(mensaje) {
                 $("#inp11").html(mensaje);
                  document.getElementById('inp1').value= parseFloat(document.getElementById('inp1').value)+ parseFloat(1);
                  document.getElementById('clienteid').value= parseFloat(document.getElementById('clienteid').value)+ parseFloat(1);
                  var statusConfirm =(confirm("Debe Agregar una Obra al Cliente"));
                  document.getElementById('btnmodificarcta').disabled=true;
                  if (statusConfirm == true){ 
                      $('#myModal_cliente').modal('hide');
                        document.getElementById('btncrearct').disabled=false;
                        document.getElementById('cuentadom').value='';
                        document.getElementById('ctaid').value='';
                        document.getElementById('ctacoment').value='';
                      $('#myModal_cuenta').modal('show');
                  }else{
                     var cliente= document.getElementById('inp1').value;
                      tp='Restringido';
                     $.post("/sda/clientes/restrcliente", {cliente:cliente}, function(mensaje) {
                     $("#inp11").html(mensaje);
                   });
                     alert('Cliente Restringido Por No Registrar su Obra');
                  }
               var id=0;
                 $.post("/sda/clientes/idcl", {cliente:cliente}, function(mensaje) {
                 $("#inp11").html(mensaje);
                 id= parseInt(mensaje[0]['cliente']);
                document.getElementById('clienteid').value=mensaje[0]['cliente'];
                $("#tabla_clientes").append("<tr class='dev'><tr class='dev'><td style='text-align: center';><a href=#myModal_detalles data-toggle=modal data-target=#myModal_detalles>Editar</td><td style='text-align: center'>"+id+"</td><td style='text-align: center'>"+razon+"</td><td style='text-align: center'>"+rfc+"</td><td style='text-align: center'>"+tp+"</td></tr>");
                });
             });
           }
         }
         function filtros() {
	          var cliente= document.getElementById('clt').value;
	          var razon= document.getElementById('razon_social').value;
	          var rfc= document.getElementById('rfc').value;
	          var status = document.getElementById('status').value;
	          var valor='';
              if(status=='1'){
                valor='';
              }if(status=='2'){
                valor='Normal';
              }if(status=='3'){
                valor='Restringido';
              }
              $("#tabla_clientes").find("tr:gt(0)").remove(); 
             $.post("/sda/clientes/flt",{cliente:cliente,razon:razon,rfc:rfc,status:valor},
                function(mensaje) {
                   $("#rfc").html(mensaje);
                   for (var i=0, total = mensaje.length; i < total; i++) {
                      $("#tabla_clientes").append("<tr><tr><td style='text-align: center';><a href=#myModal_detalles data-toggle=modal data-target=#myModal_detalles>Editar</td><td style='text-align: center'>"+mensaje[i]["cliente"]+"</td><td style='text-align: center'>"+mensaje[i]["razon_social"]+"</td><td style='text-align: center'>"+mensaje[i]["rfc"]+"</td><td style='text-align: center'>"+mensaje[i]["restringir"]+"</td></tr>");
                    }
                });
         }
           