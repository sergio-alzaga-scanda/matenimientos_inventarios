<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
         <style>
            *{
                font-family: Calibri, Arial, Helvetica, sans-serif;
            }

           body{
               padding:1 0.5cm;
               font-size: 12pt;
               color:#575757;
           }
           @page { 
                size:8.5in 11in; 
                margin:0.3in 0.7in;
           }
             h2,span,p.nomarg{
                 margin:0;
             }
            
            table{
                width:7.1in;
                margin: 0 auto;
                border-collapse: collapse;
            }
            
            td{
                vertical-align:middle;
            }
            
             .logo{
                 width:8cm;
             }
            .mayusculas{
                text-transform: uppercase
            }
             hr{
                 border-top: solid 1px #919191;
                 border-bottom: none;
                 margin-top:0.7cm;
             }
        
        </style>
        
        <table>
            <tr>
                
            </tr>
            <tr>
                <td style="padding-top:2em; text-align:justify">
                    <span>FOLIO:</span> <br>
                    <span> <strong>{{$folio}}</strong> </span><br>
                    <span>COMENTARIO:</span> <br>
                    <span> <strong>{{$comentario}}</strong> </span><br>
                    <span>FECHA:</span> <br>
                    <span> <strong>{{$fecha}}</strong> </span><br>
                    <span>GESTOR:</span> <br>
                    <span> <strong>{{$user}}</strong> </span><br>
                    
                     
                </td>
            </tr>
            
        </table>
    </body>
</html>