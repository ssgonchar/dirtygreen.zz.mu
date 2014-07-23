<div class="row">
    <div class="col-md-12">
        <div class="col-md-2">
            <!-- Button trigger modal -->
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
                New email
            </button>

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">New email</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <form role="form">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Subject</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">From</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">To</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                                        </div> 
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Cc</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                                        </div>        
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Bcc</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                                        </div>
                                    </form>                            
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Text</label><br>
                                        <textarea id="email_text"></textarea>
                                    </div>                                

                                </div>                                
                            </div>
                            <div class="row">
                                <div  class="col-md-12">
                                    <form action="/file-upload"
                                          class="dropzone"
                                          id="my-awesome-dropzone"></form>    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save and send</button>
                            <button type="button" class="btn btn-success">Create template</button>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <label>Filter by BIZ</label>
                        <input type="text" class="form-control" placeholder="BIZ keyword">

                    </div>
                </div> 
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group">
                        <a href="#" class="list-group-item active">
                            Last updated BIZs
                        </a>                        
                        <li class="list-group-item">
                            <span class="badge">14</span>
                            Cras justo odio
                        </li>
                        <li class="list-group-item">
                            <span class="badge">14</span>
                            Cras justo odio
                        </li>
                        <li class="list-group-item">
                            <span class="badge">14</span>
                            Cras justo odio
                        </li>
                        <li class="list-group-item">
                            <span class="badge">14</span>
                            Cras justo odio
                        </li>
                        <li class="list-group-item">
                            <button class="btn btn-default btn-xs">more BIZs...</button>
                        </li>
                    </ul>
                </div> 
            </div>
        </div>
        <div class="col-md-10">2
        </div>
    </div>
</div>