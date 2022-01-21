<!DOCTYPE html>
<html>
<head>
    <title>Pegasus Socks</title>

    <style type="text/css">
        table, td, th {  
  border: 1px solid black;
  text-align: left;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 15px;
}
    </style>
</head>
<body>
   



    <table >
        <?php 
            
            $output = '';
            $str = '';
            $etr = '';
            $sl = 1;

           

            $new_arr=[];

          foreach($get_data as $key=>$single_get_data){   

            array_push($new_arr, $single_get_data->shocks_code);

           }  

          // print_r($new_arr);

            for($i=0; $i<count($get_data); $i++){
               if($sl == 1){
                     $output.="<tr>";
               }
                $sl++;

                    

                    $output.="<td style='text-align:center;font-weight:bold;'>$new_arr[$i]</td>";
                
               

               if(($i % 6) == 0){

                     $output.="</tr>";
                     $sl = 1;
               }
              
            }

            $output .= "<td style='text-align:center;font-weight:bold;'>$i</td>";



            // if(($i-1)%6 != 0){
            //     $output.="<td style='text-align:center;font-weight:bold;'>123</td><td style='text-align:center;font-weight:bold;'>123</td><td style='text-align:center;font-weight:bold;'>123</td><td style='text-align:center;font-weight:bold;'>123</td><td style='text-align:center;font-weight:bold;'>123</td></tr>";
            // }
            

            
            // echo $output;                           


            echo  $output;
          
        ?>
       

       

    </table>



</body>
</html>

