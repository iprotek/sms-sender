<!-- Title: Kaizen Suggestion Approval | 000000123 - Sample -->
<html>
    <head>

        <style> 
            .text-right{
                padding-right:10px;
                text-align: right;

            }
        </style>
    </head>
    <?php
        $lastNumberSeven = function($str){
            return substr('0000000'.$str, -7);
        };

    ?>
    <body> 
        <table>
            <tr>
                <th class="text-right">TICKET NO:</th>
                <td> {{ $lastNumberSeven($ticket->id) }}</td>
            </tr>
            <tr>
                <th class="text-right">TITLE:</th>
                <td> {{ $ticket->title }}</td>
            </tr>
            <tr>
                <th class="text-right">APP NAME:</th>
                <td>{{ $ticket->app_name }}</td>
            </tr>
            <tr>
                <th class="text-right">URL:</th>
                <td>{{ $ticket->app_url }}</td>
            </tr>
            <tr>
                <th class="text-right">RESPONSE LINK:</th>
                <td>
                    <div style="color:red; font-weight:bold;">-Please dont share this link-</div>
                    {{ $ticket->response_url }}
                </td>
            </tr>
            @if($status)
                <tr>
                    <th class="text-right">STATUS</th>
                    <td> {{ $status->status_name }} </td>
                </tr>
                <tr>
                    <th class="text-right"> Remarks </th>
                    <td>  {{ $status->remarks }} </td>
                </tr>

            @else
                <tr>
                    <th class="text-right">STATUS</th>
                    <td> -- PENDING -- </td>
                </tr>
                <tr>
                    <th class="text-right" > REMARKS </th>
                    <td style="color:orange;">Waiting for your response using response link </td>
                </tr>
            @endif
            @if($latest_message)
                <tr>
                    <th>LATEST CHAT</th>
                    <td>{{ $latest_message->message }}</td>
                </tr>
            @endif
        </table>
    </body>
</html>