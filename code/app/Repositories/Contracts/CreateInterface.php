<?php 

namespace ec5\Repositories\Contracts;

use ec5\Models\Projects\ProjectStructure;

/**
 * Interface RepositoryInterface
 * 
 */
interface CreateInterface {

    /**
     * @param ProjectStructure $projectStructure
     * @return mixed
     */
    public function create(ProjectStructure $projectStructure);

}
